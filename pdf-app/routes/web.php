<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfMergeController;
use App\Http\Controllers\PdfEncryptController;

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

// PDF Merge Routes
Route::get('/merge', function () {return view('pdf.merge');})->middleware('auth')->name('pdf.merge');
Route::post('/merge', [PdfMergeController::class, 'upload'])->middleware('auth')->name('pdf.merge.upload');

// PDF Encrypt Routes
Route::view('/encrypt', 'pdf.encrypt')->name('pdf.encrypt');
Route::post('/encrypt', [PdfEncryptController::class, 'encrypt'])->name('pdf.encrypt.upload');
    