<?php

declare(strict_types = 1);

use App\Http\Controllers\BooksController;
use App\Http\Controllers\CheckBatchesController;
use App\Http\Controllers\CreateBookController;
use App\Http\Controllers\FailWorkController;
use App\Http\Controllers\GetAssetsWorkController;
use App\Http\Controllers\GetStorylineWorkController;
use App\Http\Controllers\StoreAssetsController;
use App\Http\Controllers\UpdateStorylineController;
use App\Http\Controllers\ViewBookController;
use Illuminate\Support\Facades\Route;

Route::get('/books', BooksController::class);
Route::get('/book/{book}', ViewBookController::class);
Route::post('/book/create', CreateBookController::class);
Route::post('/check/batches', CheckBatchesController::class);

Route::get('/work/storyline', GetStorylineWorkController::class);
Route::get('/work/assets', GetAssetsWorkController::class);

Route::post('/work/{book}/storyline', UpdateStorylineController::class);
Route::post('/work/assets', StoreAssetsController::class);

Route::post('/work/{book}/failure', FailWorkController::class);
