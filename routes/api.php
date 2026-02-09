<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\ApplicationController;

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

    Route::post('/pushLaLongtitude', [AuthController::class, 'pushLaLongtitude']);

    // Route
    // Client
    Route::middleware('role_api:majikan')->group(function () {
        // Cari Mitra
        Route::get('/seek-mitra', [PartnerController::class, 'allPartner']);
        Route::get('/seek-mitra/my-favorites', [PartnerController::class, 'myFavoriteServants']);
        Route::get('/seek-mitra/detail/{id}', [PartnerController::class, 'showPartner']);
        Route::post('/seek-mitra/detail/{id}/hire', [PartnerController::class, 'hirePartner']);
        Route::post('/seek-mitra/{servant}/favorite', [PartnerController::class, 'toggleFavoriteServant']);

        // Kelola Pelamar
        Route::get('/all-applicant', [ApplicationController::class, 'allApplicant']);
        Route::put('/all-applicant/{application}/change', [ApplicationController::class, 'changeStatus']);

        // Kelola Pekerja
        Route::get('/all-worker', [WorkerController::class, 'allWorker']);
        Route::get('/all-worker/{id}', [WorkerController::class, 'showWorker']);
        Route::post('/all-worker/{application}/presence', [WorkerController::class, 'presenceWorker']);
        Route::put('/all-worker/{application}/presence/{salary}', [WorkerController::class, 'updatePresenceWorker']);
        Route::put('/all-worker/{application}/reject', [WorkerController::class, 'rejectWorker']);
        Route::post('/all-worker/{application}/complaint-worker', [WorkerController::class, 'complaintWorker']);
        Route::get('/complaints', [ComplaintController::class, 'allComplaintWorkers']);
        Route::put('/all-worker/{application}/uploadPayment', [WorkerController::class, 'uploadMajikan']);

        // Kelola Lowongan
        Route::apiResource('vacancy', VacancyController::class);
        Route::post('/vacancy/{vacancy}/apply-recom/{recomServant}', [ApplicationController::class, 'applyRecom']);

        // Kelola Profil
        Route::get('/profile/majikan/{id}', [ProfileController::class, 'profileMajikan']);
        Route::post('/profile/majikan/{id}/edit', [ProfileController::class, 'updateMajikan']);


    });

    // Mitra
    Route::middleware('role_api:pembantu')->group(function () {
        // Cari Lowongan
        Route::get('/seek-vacancy', [VacancyController::class, 'seekVacancy']);
        Route::get('/seek-vacancy/my-favorites',[VacancyController::class, 'myFavorites']);
        Route::get('/seek-vacancy/{id}', [VacancyController::class, 'showVacancy']);
        Route::post('/seek-vacancy/{vacancy}/favorite',[VacancyController::class, 'toggleFavorite']);

        // Lamaran
        Route::post('/apply-job', [ApplicationController::class, 'applyJob']);
        Route::get('/all-application', [ApplicationController::class, 'allApplication']);
        Route::put('/all-application/{application}/choose', [ApplicationController::class, 'chooseStatus']);

        // Kelola Pekerjaan
        Route::get('/all-work', [WorkerController::class, 'allWork']);
        Route::get('/all-work/{id}', [WorkerController::class, 'showWork']);
        Route::post('/all-work/{application}/complaint-work', [WorkerController::class, 'complaintWork']);
        Route::get('/complaints-work', [ComplaintController::class, 'allComplaintWork']);

        // Kelola Profil
        Route::get('/profile/pembantu/{id}', [ProfileController::class, 'profilePembantu']);
        Route::post('/profile/pembantu/{id}/edit', [ProfileController::class, 'updatePembantu']);
        Route::post('profile/pembantu/{id}/skill', [ProfileController::class, 'storeSkill']);
        Route::put('/profile/pembantu/{id}/skill/{skill_id}', [ProfileController::class, 'updateSkill']);
        Route::delete('/profile/pembantu/{id}/skill/{skill_id}', [ProfileController::class, 'destroySkill']);
        Route::put('/profile/pembantu/{id}/change-inval', [ProfileController::class, 'changeInval']);
        Route::put('/profile/pembantu/{id}/change-stay', [ProfileController::class, 'changeStay']);
    });

    // ALL
    Route::get('/schedule-interview', [ApplicationController::class, 'scheduleInterview']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //review
    Route::post('/reviews/{application}', [ReviewController::class, 'store']);
});
