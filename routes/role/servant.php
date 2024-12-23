<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilityController;

Route::group(['middleware' => ['role:pembantu|admin|superadmin']], function () {
    Route::get('/all-vacancy', [UtilityController::class, 'allVacancy'])->name('all-vacancy');
    Route::get('/show-vacancy/{id}', [UtilityController::class, 'showVacancy'])->name('show-vacancy');
    Route::post('/apply-job', [UtilityController::class, 'applyJob'])->name('apply-job');
});