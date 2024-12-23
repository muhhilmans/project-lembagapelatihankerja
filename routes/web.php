<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UtilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'login'])->name('login');

Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');

Route::get('storage/img/{path}/{imageName}', [UtilityController::class, 'displayImage'])->name('getImage');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard-servant', function () {
        return view('cms.dashboard.dashboard-servant');
    })->name('dashboard-servant');

    Route::get('/dashboard-employe', function () {
        return view('cms.dashboard.dashboard-employe');
    })->name('dashboard-employe');

    require __DIR__.'/role/admin.php';
    require __DIR__.'/role/employe.php';
    require __DIR__.'/role/servant.php';
    
    Route::get('contract/download/{applyJob}', [UtilityController::class, 'downloadContract'])->name('contract.download');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});