<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\VacancyController;

Route::group(['middleware' => ['role:majikan|admin|superadmin']], function () {
    Route::get('/all-servant', [UtilityController::class, 'allServant'])->name('all-servant');
    Route::get('/show-servant/{id}', [UtilityController::class, 'showServant'])->name('show-servant');
    Route::post('/show-servant/{id}/hire', [ApplicationController::class, 'hireServant'])->name('hire-servant');

    Route::resource('vacancies', VacancyController::class)->except('create', 'edit');
    
    // Route::put('vacancies/{vacancy}/{user}/change', [ApplicationController::class, 'changeStatus'])->name('vacancies.change');
    // Route::put('vacancies/{vacancy}/{user}/upload', [ApplicationController::class, 'uploadContract'])->name('vacancies.upload');

    Route::get('/applicant-hire', [UtilityController::class, 'hireApplicant'])->name('applicant-hire');
    Route::put('/applicant-hire/{id}/contract', [ApplicationController::class, 'hireContract'])->name('applicant-hire.contract');
    Route::put('/applicant-hire/{id}/reject', [ApplicationController::class, 'hireReject'])->name('applicant-hire.reject');

    Route::post('/vacancies/{vacancy}/{user}/recommendation', [ApplicationController::class, 'applyRecom'])->name('apply.recommendation');

    Route::get('/applicant-indie', [UtilityController::class, 'indieApplicant'])->name('applicant-indie');
    Route::put('/applicant-indie/{user}/{vacancy}/change', [ApplicationController::class, 'changeStatus'])->name('applicant-indie.change');
    Route::put('/applicant-indie/{user}/{vacancy}/upload', [ApplicationController::class, 'uploadContract'])->name('applicant-indie.upload');
});