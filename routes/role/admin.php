<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\EmployeController;
use App\Http\Controllers\User\ServantController;
use App\Http\Controllers\TrackingController;

Route::group(['middleware' => ['role:admin|superadmin|owner']], function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::resource('users-employe', EmployeController::class);
    Route::put('users-employe/{user}/change', [EmployeController::class, 'changeStatus'])->name('users-employe.change');
    Route::resource('users-servant', ServantController::class);
    Route::put('users-servant/{user}/change', [ServantController::class, 'changeStatus'])->name('users-servant.change');
    Route::post('users-servant/{user}/skill/store', [ServantController::class, 'storeSkill'])->name('users-servant.store.skill');
    Route::put('users-servant/{user}/skill/{skill}/update', [ServantController::class, 'updateSkill'])->name('users-servant.update.skill');
    Route::delete('users-servant/{user}/skill/{skill}/delete', [ServantController::class, 'destroySkill'])->name('users-servant.destroy.skill');

    Route::resource('professions', ProfessionController::class)->except('create', 'show', 'edit');

    Route::post('/vacancy/{id}/recommendation', [UtilityController::class, 'storeRecom'])->name('vacancy.recommendation');

    Route::put('complaints/{id}/change', [ComplaintController::class, 'changeStatus'])->name('complaints.change');

    Route::post('worker-all/download', [WorkerController::class, 'downloadPdf'])->name('worker.download');

    Route::resource('blogs', BlogController::class);

    Route::resource('salaries', SalaryController::class)->except('create', 'show', 'edit');

    Route::put('/worker/{app}/salary/{salary}/upload-admin', [WorkerController::class, 'uploadAdmin'])->name('payment-admin.upload');

    Route::put('/worker/{app}/change-schema', [WorkerController::class, 'changeSchema'])->name('worker.change-schema');
});

Route::group(['middleware' => ['role:superadmin|owner']], function () {
    Route::resource('users-admin', AdminController::class)->except('create');
});

Route::group(['middleware' => ['role:superadmin']], function () {
    Route::resource('vouchers', VoucherController::class)->except(['create', 'show', 'edit']);
    Route::put('vouchers/{id}/change', [VoucherController::class, 'changeStatus'])->name('vouchers.change');
    Route::get('tracking', [TrackingController::class, 'index'])->name('tracking.index');
});