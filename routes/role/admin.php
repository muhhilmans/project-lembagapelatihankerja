<?php

use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\EmployeController;
use App\Http\Controllers\User\ServantController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['role:admin|superadmin|owner']], function () {
    Route::get('/dashboard', function () {
        return view('cms.dashboard.dashboard');
    })->name('dashboard');

    Route::resource('users-employe', EmployeController::class);
    Route::resource('users-servant', ServantController::class);
    Route::put('users-servant/{user}/change', [ServantController::class, 'changeStatus'])->name('users-servant.change');
});

Route::group(['middleware' => ['role:superadmin|owner']], function () {
    Route::resource('users-admin', AdminController::class)->except('create');
});