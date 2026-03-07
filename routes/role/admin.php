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
use App\Http\Controllers\ReviewAdminController;

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
    Route::put('/worker/{app}/salary-contract/upload-admin', [WorkerController::class, 'uploadAdminContract'])->name('payment-admin-contract.upload');

    Route::put('/worker/{app}/change-schema', [WorkerController::class, 'changeSchema'])->name('worker.change-schema');
    Route::put('/worker/{app}/upload-contract-admin', [WorkerController::class, 'uploadContractWorker'])->name('worker.upload-contract-admin');

    Route::put('/worker/{app}/extend-warranty', [WorkerController::class, 'extendWarranty'])->name('worker.extend-warranty');
    Route::put('/worker/{app}/swap-servant', [WorkerController::class, 'swapServant'])->name('worker.swap-servant');
    Route::put('/worker/{app}/end-contract', [WorkerController::class, 'endContract'])->name('worker.end-contract');
    Route::put('/worker/{app}/extend-contract', [WorkerController::class, 'extendContract'])->name('worker.extend-contract');

    // Schemes Route
    Route::get('/schemes', [App\Http\Controllers\SchemeController::class, 'index'])->name('schemes.index');
    Route::post('/schemes/store', [App\Http\Controllers\SchemeController::class, 'store'])->name('schemes.store');
    Route::put('/schemes/{id}/update', [App\Http\Controllers\SchemeController::class, 'update'])->name('schemes.update');
    Route::delete('/schemes/{id}/destroy', [App\Http\Controllers\SchemeController::class, 'destroy'])->name('schemes.destroy');

    // Garansi Route
    Route::get('/garansis', [App\Http\Controllers\GaransiController::class, 'index'])->name('garansis.index');
    Route::post('/garansis/store', [App\Http\Controllers\GaransiController::class, 'store'])->name('garansis.store');
    Route::put('/garansis/{id}/update', [App\Http\Controllers\GaransiController::class, 'update'])->name('garansis.update');
    Route::delete('/garansis/{id}/destroy', [App\Http\Controllers\GaransiController::class, 'destroy'])->name('garansis.destroy');
});

Route::group(['middleware' => ['role:superadmin|owner']], function () {
    Route::resource('users-admin', AdminController::class)->except('create');
});

Route::group(['middleware' => ['role:superadmin']], function () {
    Route::resource('vouchers', VoucherController::class)->except(['create', 'show', 'edit']);
    Route::put('vouchers/{id}/change', [VoucherController::class, 'changeStatus'])->name('vouchers.change');
    Route::get('tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('tracking/locations', [TrackingController::class, 'getLocations'])->name('tracking.locations');

    Route::get('reviews', [ReviewAdminController::class, 'index'])->name('reviews.index');
});