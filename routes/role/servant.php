<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilityController;

Route::group(['middleware' => ['role:pembantu|admin|superadmin']], function () {
    Route::get('/all-vacancy', [UtilityController::class, 'allVacancy'])->name('all-vacancy');
    Route::get('/show-vacancy/{id}', [UtilityController::class, 'showVacancy'])->name('show-vacancy');
    Route::post('/apply-job', [ApplicationController::class, 'applyJob'])->name('apply-job');
    Route::put('vacancies/{vacancy}/{user}/change', [ApplicationController::class, 'changeStatus'])->name('vacancies.change');

    Route::get('/application-hire', [UtilityController::class, 'hireApplication'])->name('application-hire');
    Route::put('/applicant-hire/{id}/change', [ApplicationController::class, 'changeStatusHire'])->name('applicant-hire.change');
    Route::get('/application-indie', [UtilityController::class, 'indieApplication'])->name('application-indie');
});