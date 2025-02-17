<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
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
    
    Route::put('profile/{user}/change-inval', [ProfileController::class, 'changeInval'])->name('profile.change-inval');
    Route::put('profile/{user}/change-stay', [ProfileController::class, 'changeStay'])->name('profile.change-stay');
});