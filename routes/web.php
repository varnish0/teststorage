<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;

Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
Route::put('/photos/{photo}', [PhotoController::class, 'update'])->name('photos.update');
Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

Route::get('/doc', [PhotoController::class, 'index'])->name('doc.index');
Route::post('/doc', [PhotoController::class, 'store'])->name('doc.store');
Route::put('/doc/{doc}', [PhotoController::class, 'update'])->name('doc.update');
Route::delete('/doc/{doc}', [PhotoController::class, 'destroy'])->name('doc.destroy');