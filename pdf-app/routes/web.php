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
use App\Http\Controllers\PdfExtractController;

use App\Http\Controllers\PdfRotateController;
use App\Http\Controllers\PdfReorderController;
use App\Http\Controllers\PdfSplitController;
use App\Http\Controllers\PdfTextExtractController;
use App\Http\Controllers\PdfAddPageController;


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


    //Export the guide as PDF
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


    // PDF Rotate Routes
    Route::get('/rotate', [PdfRotateController::class, 'show'])->name('pdf.rotate');
    Route::post('/rotate', [PdfRotateController::class, 'rotate']);



    // PDF Delete Routes
    Route::get('/delete', [PdfDeleteController::class, 'show'])->name('pdf.delete');
    Route::post('/delete', [PdfDeleteController::class, 'delete'])->name('pdf.delete.upload');
    
    // PDF Extract Routes
    Route::view('/extract', 'pdf.extract')->name('pdf.extract');
    Route::post('/extract/upload', [PdfExtractController::class, 'upload'])->name('pdf.extract.upload');
    
    // PDF Reorder Routes
    Route::view('/reorder', 'pdf.reorder')->name('pdf.reorder');
    Route::post('/reorder/upload', [PdfReorderController::class, 'reorder'])->name('pdf.reorder.upload');

    // PDF Split Routes
    Route::view('/split', 'pdf.split')->name('pdf.split');
    Route::post('/split/upload', [PdfSplitController::class, 'split'])->name('pdf.split.upload');

    // PDF Text Extract Routes
    Route::view('/extract-text', 'pdf.extract-text')->name('pdf.extract-text');
    Route::post('/extract-text/upload', [PdfTextExtractController::class, 'extract'])->name('pdf.extract-text.upload');

    // PDF Add Page Routes
    Route::view('/add-page', 'pdf.add-page')->name('pdf.add-page');
    Route::post('/add-page/upload', [PdfAddPageController::class, 'add'])->name('pdf.add-page.upload');




});

// Google Authentication Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
require __DIR__.'/auth.php';

use Illuminate\Support\Facades\File;

Route::get('/lang/force/{lang}', function ($lang) {
    if (!in_array($lang, ['en', 'sk'])) abort(403);

    $configPath = config_path('app.php');
    $content = File::get($configPath);

    $content = preg_replace("/'locale'\s*=>\s*['\"](\w+)['\"]/", "'locale' => '{$lang}'", $content);
    $content = preg_replace("/'fallback_locale'\s*=>\s*['\"](\w+)['\"]/", "'fallback_locale' => '{$lang}'", $content);

    File::put($configPath, $content);

    // Clear cached config (if allowed)
    shell_exec('php artisan config:clear');

    return redirect()->back()->with('status', "Jazyk prepisaný na {$lang}.");
})->name('lang.force');

