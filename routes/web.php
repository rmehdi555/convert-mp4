<?php

use App\Http\Controllers\ConvertController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('convert.index');
});

// Convert routes
Route::get('convert', [ConvertController::class, 'index'])->name('convert.index');
Route::get('convert/create', [ConvertController::class, 'create'])->name('convert.create');
Route::post('convert', [ConvertController::class, 'store'])->name('convert.store');
Route::get('convert/{convertMp4}', [ConvertController::class, 'show'])->name('convert.show');
Route::get('convert/{convertMp4}/download', [ConvertController::class, 'download'])->name('convert.download');
