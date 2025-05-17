<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\PdfMergeController;
use App\Http\Controllers\PdfEncryptController;
use App\Http\Controllers\PdfDecryptController;

use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\EditHistoryController;

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\PdfDeleteController;


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

    Route::post('/api-token/generate', [ApiKeyController::class, 'store'])->name('api-token.generate');
});

    // Google Authentication Routes
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

    // Added Static Pages
    Route::view('/guide', 'guide.index')->name('guide');
    Route::view('/documentation', 'documentation')->name('documentation');
    
    
    //EditHistiory Route to EditHistoryController
    //History of PDF edits
    Route::get('/edit-history', [EditHistoryController::class, 'index'])->name('edit.history');

    //LoginHistory Route to LoginHistoryController
    //History of user logins
    Route::get('/login-history', [LoginHistoryController::class, 'index'])->name('login.history');

    //Route to delete or export to CSV selected logs
    Route::post('/edit-history/actions', [EditHistoryController::class, 'bulkAction'])->name('history.usage.action');


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
});

require __DIR__.'/auth.php';


// PDF Encrypt Routes
Route::view('/encrypt', 'pdf.encrypt')->name('pdf.encrypt');
Route::post('/encrypt', [PdfEncryptController::class, 'encrypt'])->name('pdf.encrypt.upload');
    
// PDF Decrypt Routes
Route::view('/decrypt', 'pdf.decrypt')->name('pdf.decrypt');
Route::post('/decrypt', [PdfDecryptController::class, 'decrypt'])->name('pdf.decrypt.upload');

// PDF Delete Routes
Route::get('/delete', [PdfDeleteController::class, 'show'])->name('pdf.delete');
Route::post('/delete', [PdfDeleteController::class, 'delete'])->name('pdf.delete.upload');


