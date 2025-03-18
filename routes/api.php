<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\PartnerController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register-employe', [AuthController::class, 'storeEmployeRegister']);
Route::get('/professions', [AuthController::class, 'getProfessions']);
Route::post('/register-servant', [AuthController::class, 'storeServantRegister']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/verify-otp/resend', [AuthController::class, 'resendOtpVerification']);

Route::middleware('jwt.auth')->group(function () {

    // Client
    Route::middleware('role_api:majikan')->group(function () {
        // Cari Mitra
        Route::get('/seek-mitra', [PartnerController::class, 'allPartner']);
        Route::get('/seek-mitra/detail/{id}', [PartnerController::class, 'showPartner']);
        Route::post('/seek-mitra/detail/{id}/hire', [PartnerController::class, 'hirePartner']);

        // Kelola Pelamar
        Route::get('/all-applicant', [ApplicationController::class, 'allApplicant']);

        // Kelola Lowongan
        Route::apiResource('vacancy', VacancyController::class);
        Route::post('/vacancy/{vacancy}/apply-recom/{recomServant}', [ApplicationController::class, 'applyRecom']);

        // Kelola Profil
        Route::get('/profile/majikan/{id}', [ProfileController::class, 'profileMajikan']);
        Route::put('/profile/majikan/{id}/edit', [ProfileController::class, 'updateMajikan']);
    });

    // Mitra
    Route::middleware('role_api:pembantu')->group(function () {
        // Cari Lowongan
        Route::get('/seek-vacancy', [VacancyController::class, 'seekVacancy']);
        Route::get('/seek-vacancy/{id}', [VacancyController::class, 'showVacancy']);

        // Lamaran
        Route::post('/apply-job', [ApplicationController::class, 'applyJob']);

        // Kelola Profil
        Route::get('/profile/pembantu/{id}', [ProfileController::class, 'profilePembantu']);
        Route::put('/profile/pembantu/{id}/edit', [ProfileController::class, 'updatePembantu']);
        Route::post('profile/pembantu/{id}/skill', [ProfileController::class, 'storeSkill']);
        Route::put('/profile/pembantu/{id}/skill/{skill_id}', [ProfileController::class, 'updateSkill']);
        Route::delete('/profile/pembantu/{id}/skill/{skill_id}', [ProfileController::class, 'destroySkill']);
        Route::put('/profile/pembantu/{id}/change-inval', [ProfileController::class, 'changeInval']);
        Route::put('/profile/pembantu/{id}/change-stay', [ProfileController::class, 'changeStay']);
    });

    // ALL
    Route::post('/logout', [AuthController::class, 'logout']);
});
