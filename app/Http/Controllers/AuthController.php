<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;

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

        return view('login');
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

            if ($user->is_active == 1) {
                if ($user->roles->first()->name == 'pembantu') {
                    return redirect()->intended('/dashboard-servant');
                } elseif ($user->roles->first()->name == 'majikan') {
                    return redirect()->intended('/dashboard-employe');
                } else {
                    return redirect()->intended('/dashboard');
                }
            } else {
                Auth::logout();

                Alert::error('Akun belum aktif!', 'Silahkan hubungi Admin..');
                return redirect()->back();
            }
        }

        Alert::error('Gagal!', 'Akun yang dimasukkan salah!');
        return redirect()->back();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Alert::success('Berhasil!', 'Anda berhasil logout!');
        return redirect()->to('/');
    }
}
