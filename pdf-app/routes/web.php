<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfExportController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Google Authentication Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// Added Static Pages
Route::view('/guide', 'guide.index')->name('guide');
Route::view('/documentation', 'documentation')->name('documentation');
Route::view('/history-usage', 'history-usage')->name('history.usage');
Route::view('/history-login', 'history-login')->name('history.login');


//to export the guide as PDF
Route::get('/guide/export', [PdfExportController::class, 'exportPdf'])->name('guide.export');
