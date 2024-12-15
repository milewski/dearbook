<?php

declare(strict_types = 1);

use App\Http\Controllers\BooksController;
use App\Http\Controllers\CheckBatchesController;
use App\Http\Controllers\CreateBookController;
use App\Http\Controllers\GetWorkController;
use App\Http\Controllers\ViewBookController;
use Illuminate\Support\Facades\Route;

Route::get('/books', BooksController::class);
Route::get('/book/{book}', ViewBookController::class);
Route::post('/book/create', CreateBookController::class);
Route::post('/check/batches', CheckBatchesController::class);

Route::post('/get/work', GetWorkController::class);
