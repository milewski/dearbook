<?php

declare(strict_types = 1);

use App\Http\Controllers\BooksController;
use App\Http\Controllers\CheckBatchesController;
use App\Http\Controllers\CreateBookAdvancedController;
use App\Http\Controllers\CreateBookController;
use App\Http\Controllers\DeleteBookController;
use App\Http\Controllers\FailWorkController;
use App\Http\Controllers\GetAssetsWorkController;
use App\Http\Controllers\MyBooksController;
use App\Http\Controllers\StoreAssetsController;
use App\Http\Controllers\StoreSpeechController;
use App\Http\Controllers\ViewBookController;
use Illuminate\Support\Facades\Route;

Route::get('/books', BooksController::class);
Route::get('/book/{book}', ViewBookController::class);
Route::post('/book/create', CreateBookController::class);
Route::post('/book/create/advanced', CreateBookAdvancedController::class);
Route::post('/check/batches', CheckBatchesController::class);

Route::get('/my/books', MyBooksController::class);
Route::post('/book/{book}/delete', DeleteBookController::class);

Route::get('/work/assets', GetAssetsWorkController::class);
Route::post('/work/{book}/assets', StoreAssetsController::class);
Route::post('/work/{book}/speech', StoreSpeechController::class);
Route::post('/work/{book}/failure', FailWorkController::class);
