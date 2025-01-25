<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WorkerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');

Route::get('/select-register', [AuthController::class, 'selectRegister'])->name('select-register');
Route::get('/select-register/tc-employe', [AuthController::class, 'tcEmployeRegister'])->name('register-tc-employe');
Route::get('/select-register/employe', [AuthController::class, 'employeRegister'])->name('register-employe');
Route::post('/select-register/employe/store', [AuthController::class, 'storeEmployeRegister'])->name('store-employe-register');
Route::get('/select-register/tc-servant', [AuthController::class, 'tcServantRegister'])->name('register-tc-servant');
Route::get('/select-register/servant', [AuthController::class, 'servantRegister'])->name('register-servant');
Route::post('/select-register/servant/store', [AuthController::class, 'storeServantRegister'])->name('store-servant-register');
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/email/verify', function () {
    return view('auth.verify-notice');
})->middleware('auth')->name('verification.notice');
Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])
    ->middleware('auth')
    ->name('verification.resend');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('forgot.password');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.password.post');
Route::get('/password-reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset.password');
Route::post('/password-reset', [AuthController::class, 'resetPassword'])->name('reset.password.post');

Route::get('storage/img/{path}/{imageName}', [UtilityController::class, 'displayImage'])->name('getImage');
Route::get('/professions/pdf/{id}', [UtilityController::class, 'pdfProfession'])->name('pdfProfession');

Route::get('storage/{path}/{fileName}', [UtilityController::class, 'displayFile'])->name('getFile');

Route::get('/all-blogs', [HomeController::class, 'allBlogs'])->name('all-blogs');
Route::get('/blog/{slug}', [HomeController::class, 'blogDetail'])->name('blog-detail');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard-servant', [DashboardController::class, 'dashboardServant'])->name('dashboard-servant');

    Route::get('/dashboard-employe', [DashboardController::class, 'dashboardEmploye'])->name('dashboard-employe');

    require __DIR__ . '/role/admin.php';
    require __DIR__ . '/role/employe.php';
    require __DIR__ . '/role/servant.php';
    Route::put('/applicant-hire/{id}/change', [ApplicationController::class, 'changeStatusHire'])->name('applicant-hire.change');
    Route::put('/applicant-hire/{id}/contract', [ApplicationController::class, 'hireContract'])->name('applicant-hire.contract');
    Route::put('/applicant-hire/{id}/reject', [ApplicationController::class, 'hireReject'])->name('applicant-hire.reject');
    Route::put('vacancies/{vacancy}/{user}/change', [ApplicationController::class, 'changeStatus'])->name('vacancies.change');

    Route::get('contract/download/{applicationId}', [ApplicationController::class, 'downloadContract'])->name('contract.download');

    Route::resource('complaints', ComplaintController::class);

    Route::get('/worker-all', [WorkerController::class, 'allWorker'])->name('worker-all');
    Route::get('/worker/{id}', [WorkerController::class, 'showWorker'])->name('worker.show');
    Route::post('/worker/{id}/presence', [WorkerController::class, 'presenceWorker'])->name('worker.presence.store');

    Route::get('/profile/{id}', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::put('/profile/{id}/update-servant', [ProfileController::class, 'updateServant'])->name('profile-servant.update');
    Route::post('/profile/{id}/store-skill', [ProfileController::class, 'storeSkill'])->name('profile-servant.store-skill');
    Route::put('/profile/{id}/update-skill/{skill_id}', [ProfileController::class, 'updateSkill'])->name('profile-servant.update-skill');
    Route::delete('/profile/{id}/destroy-skill/{skill_id}', [ProfileController::class, 'destroySkill'])->name('profile-servant.destroy-skill');

    Route::put('/profile/{id}/update-employe', [ProfileController::class, 'updateEmploye'])->name('profile-employe.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
