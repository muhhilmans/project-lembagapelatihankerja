<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Models\Profession;
use Illuminate\Http\Request;
use App\Models\EmployeDetail;
use App\Models\ServantDetail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function responseWithToken($token, $user)
    {
        return $this->successResponse([
            'user' => $user->makeHidden(['roles', 'access_token', 'created_at', 'updated_at']),
            'role' => $user->getRoleNames(),
            'access_token' => $token,
            'type' => 'bearer'
        ], 'Login berhasil');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $fieldType = filter_var($request->account, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $fieldType => $request->account,
            'password' => $request->password,
        ];

        $user = User::where($fieldType, $request->account)->first();

        if (!$user) {
            return $this->errorResponse('Akun tidak terdaftar.', [], 401);
        }

        if (!empty($user->access_token)) {
            try {
                auth('api')->setToken($user->access_token)->invalidate();
            } catch (\Exception $e) { }
        }

        if (!$token = auth('api')->setTTL(43200)->attempt($credentials)) {
            return $this->errorResponse('Kombinasi akun dan password salah.', [], 401);
        }

        $user = auth('api')->user();

        if ($user->email_verified_at == null) {
            auth('api')->logout();
            return $this->errorResponse('Email belum diverifikasi!', ['email' => $user->email], 401);
        }

        $user->forceFill(['access_token' => $token])->save();

        return $this->responseWithToken($token, $user);
    }

    public function logout()
    {
        try {
            $user = auth('api')->user();
            if ($user) {
                $user->forceFill(['access_token' => null])->save();
                auth('api')->invalidate(true);
            }
            return $this->successResponse([], 'Berhasil logout.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal logout.', [], 500);
        }
    }

    private function createUser(array $data, string $role, callable $detailsCallback)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => false,
            ]);

            $user->assignRole($role);
            $detailsCallback($user);

            $otpCode = random_int(100000, 999999);
            Otp::create([
                'user_id' => $user->id,
                'otp_code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(5),
            ]);

            DB::commit();

            $this->sendOtpEmail($user, $otpCode);

            return $this->successResponse(['user' => $user], 'Registrasi berhasil. Silakan verifikasi email Anda.', 201);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Register Error: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan sistem saat registrasi.', [], 500);
        }
    }

    public function storeEmployeRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        return $this->createUser($request->all(), 'majikan', function($user) use ($request) {
            EmployeDetail::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address
            ]);
        });
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
            return $this->validationErrorResponse($validator);
        }

        return $this->createUser($request->all(), 'pembantu', function($user) use ($request) {
            ServantDetail::create([
                'user_id' => $user->id,
                'profession_id' => $request->profession_id
            ]);
        });
    }

    public function getProfessions()
    {
        $data = Profession::all();

        if ($data->isEmpty()) {
            return $this->successResponse([], 'Belum ada data profesi.');
        }

        return $this->successResponse($data, 'Semua data profesi');
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator, 'Format data salah');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan!', [], 404);
        }

        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp_code', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return $this->errorResponse('Kode OTP salah atau sudah kedaluwarsa!', [], 422);
        }

        $otpRecord->delete();
        $user->forceFill(['email_verified_at' => now()])->save();

        return $this->successResponse([], 'Email berhasil diverifikasi.');
    }

    public function resendOtpVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = User::where('email', $request->email)->first();

        if (!is_null($user->email_verified_at)) {
            return $this->successResponse([], 'Email sudah diverifikasi sebelumnya. Silakan login.');
        }

        $recentOtp = Otp::where('user_id', $user->id)
                        ->where('created_at', '>', now()->subMinute())
                        ->exists();

        if ($recentOtp) {
            return $this->errorResponse('Harap tunggu 1 menit sebelum meminta OTP lagi.', [], 429);
        }

        Otp::where('user_id', $user->id)->delete();

        $otp = random_int(100000, 999999);
        Otp::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5)
        ]);

        $this->sendOtpEmail($user, $otp);

        return $this->successResponse([], 'Kode OTP berhasil dikirim ulang. Silakan cek email Anda.');
    }

    protected function sendOtpEmail($user, $otp) {
        try {
            Mail::send('auth.otp-email', ['otp' => $otp], function ($message) use ($user) {
                $message->to($user->email)->subject('Kode OTP Verifikasi Email');
            });
        } catch (\Exception $e) {
            Log::error("Gagal mengirim email: " . $e->getMessage());
        }
    }
}
