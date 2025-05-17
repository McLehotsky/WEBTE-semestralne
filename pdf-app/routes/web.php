<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\PdfMergeController;
use App\Http\Controllers\PdfEncryptController;
use App\Http\Controllers\PdfDecryptController;
use Symfony\Component\HttpFoundation\Cookie;
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

// PDF Merge Routes
Route::get('/merge', function () {return view('pdf.merge');})->middleware('auth')->name('pdf.merge');
Route::post('/merge', [PdfMergeController::class, 'upload'])->middleware('auth')->name('pdf.merge.upload');

// PDF Encrypt Routes
Route::view('/encrypt', 'pdf.encrypt')->name('pdf.encrypt');
Route::post('/encrypt', [PdfEncryptController::class, 'encrypt'])->name('pdf.encrypt.upload');
    
// PDF Decrypt Routes
Route::view('/decrypt', 'pdf.decrypt')->name('pdf.decrypt');
Route::post('/decrypt', [PdfDecryptController::class, 'decrypt'])->name('pdf.decrypt.upload');


// Language Switch Route


Route::get('/lang/{lang}', function ($lang) {
    if (!in_array($lang, ['sk', 'en'])) {
        abort(400);
    }

    session(['locale' => $lang]);

    // nastav cookie bez šifrovania (raw = true)
    return redirect()->back()->withCookie(
        new Cookie('locale', $lang, time() + (60 * 60 * 24 * 30), '/', null, false, false, false, 'Lax')
    );
})->name('lang.switch');



