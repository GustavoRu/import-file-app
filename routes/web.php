<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebtorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('debtor/upload', [DebtorController::class, 'showUploadForm']);
Route::post('debtor/uploadFile', [DebtorController::class, 'processFile'])->name('debtor.process');

Route::post('test', function (Request $request) {
    return response()->json(['status' => 'success']);
})->name('test');   