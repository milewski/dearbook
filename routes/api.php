<?php

use App\Http\Controllers\ViewBookController;
use Illuminate\Support\Facades\Route;

Route::get('/book/{book}', ViewBookController::class);
