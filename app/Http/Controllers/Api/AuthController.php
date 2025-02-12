<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
}
