<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\VacancyController;

Route::group(['middleware' => ['role:majikan|admin|superadmin']], function () {
    Route::get('/all-servant', [UtilityController::class, 'allServant'])->name('all-servant');
    Route::get('/show-servant/{id}', [UtilityController::class, 'showServant'])->name('show-servant');

    Route::resource('vacancies', VacancyController::class)->except('create', 'edit');
    Route::put('vacancies/{vacancy}/{user}/change', [UtilityController::class, 'changeStatus'])->name('vacancies.change');
    Route::put('vacancies/{vacancy}/{user}/upload', [UtilityController::class, 'uploadContract'])->name('vacancies.upload');
});