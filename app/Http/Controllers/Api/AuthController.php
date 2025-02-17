<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Models\EmployeDetail;
use App\Models\ServantDetail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function responseWithToken($token, $user)
    {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer',
            'expired_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        $fieldType = filter_var($validated['account'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $validated['account'],
            'password' => $validated['password'],
        ];

        $token = auth('api')->attempt($credentials);
        $user = auth('api')->user();

        if ($token) {
            if ($user->email_verified_at == null) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Email belum diverifikasi! Silahkan verifikasi email terlebih dahulu..',
                ], 401);
            }

            return $this->responseWithToken($token, auth('api')->user());
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun yang dimasukkan salah',
            ], 401);
        }
    }

    public function logout()
    {
        try {
            auth('api')->invalidate(true); // Masukkan token ke blacklist

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to logout, please try again!',
            ], 500);
        }
    }

    public function storeEmployeRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $store = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => false,
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                ], 502);
            }

            $store->assignRole('majikan');

            EmployeDetail::create([
                'user_id' => $store->id,
                'phone' => $data['phone'],
                'address' => $data['address']
            ]);

            $this->sendVerificationEmail($store);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.',
                'user'    => $store
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat registrasi, silahkan coba lagi.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    public function getProfessions()
    {
        $data = Profession::all();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function storeServantRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'profession_id' => ['required', 'exists:professions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            $store = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => false,
            ]);

            if (!$store) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                ], 502);
            }

            $store->assignRole('pembantu');

            ServantDetail::create([
                'user_id' => $store->id,
                'profession_id' => $request->profession_id
            ]);

            $this->sendVerificationEmail($store);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.',
                'user'    => $store
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat registrasi, silahkan coba lagi.',
                'error'   => $th->getMessage()
            ], 500);
        }
    }

    protected function sendVerificationEmail($user)
    {
        $verificationLink = route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        Mail::send('auth.verify', ['link' => $verificationLink], function ($message) use ($user) {
            $message->to($user->email)->subject('Verifikasi Email Anda');
        });
    }

    // Verifikasi email
    public function verifyEmail($id, $hash)
    {
        $user = User::find($id);

        if (!$user || sha1($user->email) !== $hash) {
            return redirect()->route('login')->with('error', 'Tautan verifikasi tidak valid.');
        }

        $user->update(['email_verified_at' => now()]);

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi.');
    }
}
