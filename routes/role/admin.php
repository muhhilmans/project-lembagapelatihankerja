<?php

use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\EmployeController;
use App\Http\Controllers\User\ServantController;
use App\Http\Controllers\UtilityController;

Route::group(['middleware' => ['role:admin|superadmin|owner']], function () {
    Route::get('/dashboard', function () {
        return view('cms.dashboard.dashboard');
    })->name('dashboard');

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

    Route::post('worker-all/download', [UtilityController::class, 'downloadPdf'])->name('worker.download');
});

Route::group(['middleware' => ['role:superadmin|owner']], function () {
    Route::resource('users-admin', AdminController::class)->except('create');
});