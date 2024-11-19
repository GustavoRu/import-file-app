<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebtorController;

Route::get('/', [DebtorController::class, 'showUploadForm']);

Route::get('debtor/upload', [DebtorController::class, 'showUploadForm']);
Route::post('debtor/uploadFile', [DebtorController::class, 'processFile'])->name('debtor.process');
Route::get('/debtor/show', [DebtorController::class, 'show'])->name('debtor.show');

Route::post('test', function (Request $request) {
    return response()->json(['status' => 'success']);
})->name('test');   