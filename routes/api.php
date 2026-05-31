<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\GaransiController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\VacancyController;
use App\Http\Controllers\Api\WorkerController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register-employe', [AuthController::class, 'storeEmployeRegister']);
Route::get('/professions', [AuthController::class, 'getProfessions']);
Route::post('/register-servant', [AuthController::class, 'storeServantRegister']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/verify-otp/resend', [AuthController::class, 'resendOtpVerification']);

Route::middleware('jwt.auth')->group(function () {

    Route::post('/pushLaLongtitude', [AuthController::class, 'pushLaLongtitude']);

    // ==========================================
    // ROLE: CLIENT / MAJIKAN
    // ==========================================
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
        Route::put('/all-worker/{application}/reject', [WorkerController::class, 'rejectWorker']);
        Route::post('/all-worker/{application}/complaint-worker', [WorkerController::class, 'complaintWorker']);

        // Pengaturan Gaji & Kontrak (set salary type, amounts, dates)
        Route::post('/all-worker/{application}/set-salary', [WorkerController::class, 'setSalary']);

        // Upload dokumen kontrak → status accepted
        Route::post('/all-worker/{application}/upload-contract', [WorkerController::class, 'uploadContractFile']);

        // Pembaruan Sistem Pembayaran Gaji (Contract vs Fee)
        Route::post('/all-worker/{application}/uploadPayment-contract', [WorkerController::class, 'uploadMajikanContract']);
        Route::post('/all-worker/{application}/uploadPayment-fee', [WorkerController::class, 'uploadMajikanFee']);

        // Pembaruan Operasional Kontrak Kerja
        Route::post('/all-worker/{application}/end-contract', [WorkerController::class, 'endContract']);
        Route::post('/all-worker/{application}/extend-contract', [WorkerController::class, 'extendContract']);
        Route::post('/all-worker/{application}/extend-warranty', [WorkerController::class, 'extendWarranty']);
        Route::post('/all-worker/{application}/swap-servant', [WorkerController::class, 'swapServant']);

        // Kelola Lowongan
        Route::apiResource('vacancy', VacancyController::class);
        Route::post('/vacancy/{id}/restore', [VacancyController::class, 'restore']);
        Route::post('/vacancy/{vacancy}/apply-recom/{recomServant}', [ApplicationController::class, 'applyRecom']);

        // Kelola Profil
        Route::get('/profile/majikan/{id}', [ProfileController::class, 'profileMajikan']);
        Route::post('/profile/majikan/{id}/edit', [ProfileController::class, 'updateMajikan']);
    });


    // ==========================================
    // ROLE: MITRA / PEMBANTU
    // ==========================================
    Route::middleware('role_api:pembantu')->group(function () {
        // Cari Lowongan
        Route::get('/seek-vacancy', [VacancyController::class, 'seekVacancy']);
        Route::get('/seek-vacancy/my-favorites',[VacancyController::class, 'myFavorites']);
        Route::get('/seek-vacancy/{id}', [VacancyController::class, 'showVacancy']);
        Route::post('/seek-vacancy/{vacancy}/favorite',[VacancyController::class, 'toggleFavorite']);

        // Lamaran
        Route::post('/apply-job', [ApplicationController::class, 'applyJob']);
        Route::get('/all-application', [ApplicationController::class, 'allApplication']);
        Route::get('/all-application/{application}/preview', [ApplicationController::class, 'previewApplication']);
        Route::put('/all-application/{application}/choose', [ApplicationController::class, 'chooseStatus']);

        // Kelola Pekerjaan
        Route::get('/all-work', [WorkerController::class, 'allWork']);
        Route::get('/all-work/{id}', [WorkerController::class, 'showWork']);
        Route::post('/all-work/{application}/complaint-work', [WorkerController::class, 'complaintWork']);

        // Kelola Profil
        Route::get('/profile/pembantu/{id}', [ProfileController::class, 'profilePembantu']);
        Route::post('/profile/pembantu/{id}/edit', [ProfileController::class, 'updatePembantu']);
        Route::post('profile/pembantu/{id}/skill', [ProfileController::class, 'storeSkill']);
        Route::put('/profile/pembantu/{id}/skill/{skill_id}', [ProfileController::class, 'updateSkill']);
        Route::delete('/profile/pembantu/{id}/skill/{skill_id}', [ProfileController::class, 'destroySkill']);
        Route::put('/profile/pembantu/{id}/change-inval', [ProfileController::class, 'changeInval']);
        Route::put('/profile/pembantu/{id}/change-stay', [ProfileController::class, 'changeStay']);
    });

    // ==========================================
    // ROLE: ADMIN / SUPER ADMIN (Management)
    // ==========================================
    Route::middleware('role_api:admin,superadmin')->group(function () {
        Route::put('/complaints/{id}/status', [ComplaintController::class, 'changeStatus']);
        Route::post('/complaints/{id}/resolve', [ComplaintController::class, 'resolve']);

        // Manajemen Pembayaran Pekerja
        Route::post('/all-worker/{application}/uploadPayment-admin-contract', [WorkerController::class, 'uploadAdminContract']);
        Route::post('/all-worker/{application}/uploadPayment-admin-fee/{salary}', [WorkerController::class, 'uploadAdminFee']);
        Route::post('/all-worker/{application}/verify-payment', [WorkerController::class, 'verifyMajikanPayment']);
    });

    // ==========================================
    // AKSES GLOBAL (ALL LOGGED IN USERS)
    // ==========================================
    Route::get('/schedule-interview', [ApplicationController::class, 'scheduleInterview']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Pengaduan / Complaint
    Route::get('/complaint-types', [ComplaintController::class, 'complaintTypes']);
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);

    // Fitur Live Tracking Maps
    Route::get('/tracking/locations', [TrackingController::class, 'getLocations']);

    // Master Garansi (read-only untuk semua user login, CRUD untuk admin)
    Route::get('/garansis', [GaransiController::class, 'index']);
    Route::get('/garansis/{id}', [GaransiController::class, 'show']);

    // Utility: ambil base64 dari path file
    Route::get('/file-url', function (\Illuminate\Http\Request $request) {
        $path = $request->input('path');
        if (!$path) {
            return response()->json(['success' => false, 'message' => 'Parameter path diperlukan.'], 400);
        }
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        $file = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
        $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($path);
        $base64 = base64_encode($file);

        return response()->json([
            'success' => true,
            'path'    => $path,
            'base64'  => 'data:' . $mime . ';base64,' . $base64,
        ]);
    });

    // Ulasan / Review
    Route::post('/reviews/{application}', [ReviewController::class, 'store']);

    //notifikasi
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

});
