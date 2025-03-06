<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VacancyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register-employe', [AuthController::class, 'storeEmployeRegister']);
Route::get('/professions', [AuthController::class, 'getProfessions']);
Route::post('/register-servant', [AuthController::class, 'storeServantRegister']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/verify-otp/resend', [AuthController::class, 'resendOtpVerification']);

Route::middleware('jwt.auth')->group(function () {
    Route::middleware('role_api:majikan')->group(function () {
        Route::apiResource('vacancy', VacancyController::class);

        Route::get('/profile/majikan/{id}', [ProfileController::class, 'profileMajikan']);
        Route::put('/profile/majikan/{id}/edit', [ProfileController::class, 'updateMajikan']);
    });
    
    Route::middleware('role_api:pembantu')->group(function () {
        Route::get('/seek-vacancy', [VacancyController::class, 'seekVacancy']);
        Route::get('/seek-vacancy/{id}', [VacancyController::class, 'showVacancy']);

        Route::get('/profile/pembantu/{id}', [ProfileController::class, 'profilePembantu']);
        Route::put('/profile/pembantu/{id}/edit', [ProfileController::class, 'updatePembantu']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

