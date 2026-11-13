<?php

use App\Http\Controllers\KtmController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KtmController::class, 'create'])->name('ktm.create');
Route::post('/pengajuan', [KtmController::class, 'store'])->middleware('throttle:5,1')->name('ktm.store');
Route::get('/pengajuan/{code}/selesai', [KtmController::class, 'success'])->name('ktm.success');
Route::get('/pengajuan/{code}/foto', [KtmController::class, 'submittedPhoto'])->name('ktm.photo');
Route::get('/verifikasi/{token}', [KtmController::class, 'verify'])->name('ktm.verify');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ktm', [KtmController::class, 'index'])->name('ktm.index');
    Route::get('/ktm/{ktm}/foto', [KtmController::class, 'photo'])->name('ktm.photo');
    Route::get('/ktm/{ktm}/kartu', [KtmController::class, 'card'])->name('ktm.card');
    Route::get('/ktm/{ktm}/kartu/{side}.png', [KtmController::class, 'downloadCard'])->name('ktm.download');
    Route::patch('/ktm/{ktm}', [KtmController::class, 'update'])->name('ktm.update');
    Route::delete('/ktm/{ktm}', [KtmController::class, 'destroy'])->name('ktm.destroy');
});
