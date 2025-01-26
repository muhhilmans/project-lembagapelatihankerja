<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\WorkerController;

Route::group(['middleware' => ['role:majikan|admin|superadmin']], function () {
    Route::get('/all-servant', [UtilityController::class, 'allServant'])->name('all-servant');
    Route::get('/show-servant/{id}', [UtilityController::class, 'showServant'])->name('show-servant');
    Route::post('/show-servant/{id}/hire', [ApplicationController::class, 'hireServant'])->name('hire-servant');

    Route::resource('vacancies', VacancyController::class)->except('create', 'edit');
    Route::post('/vacancies/{vacancy}/{user}/recommendation', [ApplicationController::class, 'applyRecom'])->name('apply.recommendation');
    
    Route::get('/applicant-all', [UtilityController::class, 'allApplicant'])->name('applicant-all');

    Route::get('/applicant-hire', [UtilityController::class, 'hireApplicant'])->name('applicant-hire');

    Route::get('/applicant-indie', [UtilityController::class, 'indieApplicant'])->name('applicant-indie');
    Route::put('/applicant-indie/{vacancy}/{user}/change', [ApplicationController::class, 'changeStatus'])->name('applicant-indie.change');
    Route::put('/applicant-indie/{vacancy}/{user}/upload', [ApplicationController::class, 'uploadContract'])->name('applicant-indie.upload');
    
    Route::put('/worker/{id}', [ProfileController::class, 'updateBank'])->name('update-bank');

    Route::post('/worker/{id}/presence', [WorkerController::class, 'presenceWorker'])->name('worker.presence.store');
    Route::put('/worker/{app}/presence/{salary}', [WorkerController::class, 'updatePresenceWorker'])->name('worker.presence.update');
    Route::put('/worker/{app}/salary/{salary}/upload-majikan', [WorkerController::class, 'uploadMajikan'])->name('payment-majikan.upload');
});