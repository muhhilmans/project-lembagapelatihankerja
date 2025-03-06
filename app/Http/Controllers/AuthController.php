<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Otp;
use App\Models\User;
use App\Models\Profession;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmployeDetail;
use App\Models\ServantDetail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->roles->first()->name == 'pembantu') {
                return redirect()->route('dashboard-servant');
            } elseif ($user->roles->first()->name == 'majikan') {
                return redirect()->route('dashboard-employe');
            } else {
                return redirect()->route('dashboard');
            }
        }

        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->email_verified_at == null) {
                // $this->sendOtpEmail($user, $otp);

                Alert::error('Email belum diverifikasi!', 'Silahkan verifikasi email terlebih dahulu..');
                // return redirect()->route('verification.notice');
                return redirect()->route('verify.otp', ['email' => $user->email]);
            } else {
                if ($user->is_active == 1) {
                    if ($user->roles->first()->name == 'pembantu') {
                        return redirect()->intended('/dashboard-servant');
                    } elseif ($user->roles->first()->name == 'majikan') {
                        return redirect()->intended('/dashboard-employe');
                    } else {
                        return redirect()->intended('/dashboard');
                    }
                } else {
                    Alert::error('Update Profil!', 'Silahkan update profil terlebih dahulu..');

                    if ($user->roles->first()->name == 'pembantu') {
                        return redirect()->intended('/dashboard-servant');
                    } elseif ($user->roles->first()->name == 'majikan') {
                        return redirect()->intended('/dashboard-employe');
                    } else {
                        return redirect()->intended('/dashboard');
                    }
                }
            }
        }

        Alert::error('Gagal!', 'Akun yang dimasukkan salah!');
        return redirect()->back();
    }

    public function selectRegister()
    {
        return view('auth.select-register');
    }

    public function tcEmployeRegister()
    {
        return view('auth.tc-register-employe');
    }

    public function employeRegister()
    {
        return view('auth.register-employe');
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
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        try {
            DB::transaction(function () use ($request, &$store) {
                $store = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'is_active' => false,
                ]);

                $store->assignRole('majikan');

                EmployeDetail::create([
                    'user_id' => $store->id,
                    'phone' => $request->phone,
                    'address' => $request->address
                ]);

                $otp = rand(100000, 999999);
                $expiresAt = Carbon::now()->addMinutes(5);

                Otp::create([
                    'user_id' => $store->id,
                    'otp_code' => $otp,
                    'expires_at' => $expiresAt,
                ]);

                $this->sendOtpEmail($store, $otp);
            });

            if ($store) {
                Alert::success('Berhasil!', 'Silahkan verifikasi email terlebih dahulu!');
                return redirect()->route('verify.otp', ['email' => $store->email]);
            } else {
                return back()->with('toast_error', 'Registrasi majikan gagal!');
            }
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('error', compact('data'));
        }
    }

    public function tcServantRegister()
    {
        return view('auth.tc-register-servant');
    }

    public function servantRegister()
    {
        $professions = Profession::all();

        return view('auth.register-servant', compact('professions'));
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
            return back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        try {
            DB::transaction(function () use ($request, &$store) {
                $store = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'is_active' => false,
                ]);

                $store->assignRole('pembantu');

                ServantDetail::create([
                    'user_id' => $store->id,
                    'profession_id' => $request->profession_id
                ]);

                $otp = rand(100000, 999999);
                $expiresAt = Carbon::now()->addMinutes(5);

                Otp::create([
                    'user_id' => $store->id,
                    'otp_code' => $otp,
                    'expires_at' => $expiresAt,
                ]);

                $this->sendOtpEmail($store, $otp);
            });
            if ($store) {
                Alert::success('Berhasil!', 'Silahkan verifikasi email terlebih dahulu!');
                return redirect()->route('verify.otp', ['email' => $store->email]);
            } else {
                return back()->with('toast_error', 'Registrasi pembantu gagal!');
            }
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    protected function sendOtpEmail($user, $otp)
    {
        Mail::send('auth.otp-email', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)->subject('Kode OTP Verifikasi Email');
        });
    }

    public function verifyOtpPage(Request $request)
    {
        return view('auth.verify-otp', ['email' => $request->email]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', 'OTP tidak valid!')->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('toast_error', 'Pengguna tidak ditemukan!')->withInput();
        }
        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp_code', $request->otp)
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return back()->with('toast_error', 'Kode OTP salah atau sudah kedaluwarsa!')->withInput();
        }

        $otpRecord->delete();

        $user->update(['email_verified_at' => now()]);

        return redirect()->route('login')->with('success', 'Email berhasil diverifikasi.');
    }

    public function resendOtpVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->with('toast_error', 'Email tidak valid atau belum terdaftar!')->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!is_null($user->email_verified_at)) {
            return redirect()->route('dashboard')->with('success', 'Email sudah diverifikasi.');
        }

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5);

        Otp::updateOrCreate(
            ['user_id' => $user->id],
            ['otp_code' => $otp, 'expires_at' => $expiresAt]
        );

        $this->sendOtpEmail($user, $otp);

        Alert::success('OTP Dikirim Ulang', 'Silakan cek email Anda.');
        return redirect()->back();
    }

    // protected function sendVerificationEmail($user)
    // {
    //     $verificationLink = route('verification.verify', [
    //         'id' => $user->id,
    //         'hash' => sha1($user->email),
    //     ]);

    //     Mail::send('auth.verify', ['link' => $verificationLink], function ($message) use ($user) {
    //         $message->to($user->email)->subject('Verifikasi Email Anda');
    //     });
    // }

    // // Verifikasi email
    // public function verifyEmail($id, $hash)
    // {
    //     $user = User::find($id);

    //     if (!$user || sha1($user->email) !== $hash) {
    //         return redirect()->route('login')->with('error', 'Tautan verifikasi tidak valid.');
    //     }

    //     $user->update(['email_verified_at' => now()]);

    //     return redirect()->route('login')->with('success', 'Email berhasil diverifikasi.');
    // }

    // public function resendVerificationEmail(Request $request)
    // {
    //     $user = Auth::user();

    //     if ($user->email_verified_at) {
    //         return redirect()->route('dashboard')->with('success', 'Email sudah diverifikasi.');
    //     }

    //     $this->sendVerificationEmail($user);

    //     Alert::success('Email Verifikasi Dikirim', 'Silakan cek email Anda.');
    //     return redirect()->route('verification.notice');
    // }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Alert::success('Berhasil!', 'Anda berhasil logout!');
        return redirect()->to('/');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        $existingToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if ($existingToken) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->update([
                    'token' => $token,
                    'created_at' => now(),
                ]);
        } else {
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]);
        }

        $resetLink = url('/password-reset/' . $token);

        Mail::send('auth.password-reset-email', ['link' => $resetLink], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Reset Password Notification');
        });

        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.password-reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        $reset = DB::table('password_reset_tokens')->where([
            ['email', '=', $request->email],
            ['token', '=', $request->token],
        ])->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('success', 'Password berhasil diubah!');
    }
}
