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
            'user' => $user->makeHidden(['roles', 'access_token']),
            'role' => $user->roles->pluck('name')->toArray(),
            'access_token' => $token,
            'type' => 'bearer'
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

        $user = User::where($fieldType, $validated['account'])->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun tidak terdaftar.',
            ], 401);
        }

        // if ($user && !empty($user->access_token)) {
        //     $cekToken = auth('api')->setToken($user->access_token)->authenticate();
        //     if ($cekToken) {
        //         return response()->json([
        //             'status' => 'success',
        //             'message' => 'Anda sudah login',
        //             'access_token' => $user->access_token,
        //             'type' => 'bearer'
        //         ]);
        //     }
        // }

        if (!empty($user->access_token)) {
            auth('api')->setToken($user->access_token)->invalidate();
            $user->access_token = null;
            $user->save();
        }

        if (!$token = auth('api')->setTTL(43200)->attempt($credentials)) { // 30 hari = 43200 menit
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun yang dimasukkan salah',
            ], 401);
        }

        $user = auth('api')->user();

        if ($user->email_verified_at == null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Email belum diverifikasi! Silahkan verifikasi email terlebih dahulu.',
            ], 401);
        }

        // if ($user->access_token !== $token) {
        //     $user->access_token = $token;
        //     $user->save();
        // }
        $user->access_token = $token;
        $user->save();

        return $this->responseWithToken($token, $user);
    }

    public function logout()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'User not authenticated',
                ], 401);
            }

            auth('api')->invalidate(true);

            $user->access_token = null;
            $user->save();

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
        try {
            $data = Profession::all();

            if ($data->isEmpty()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Belum ada profesi!',
                    'data'    => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Semua data profesi',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Terjadi kesalahan saat pengambilan data, silahkan coba lagi.',
                'error'   => $th->getMessage()
            ], 500);
        }
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
