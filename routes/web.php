<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/file/{filename}', [FileUploadController::class, 'get']);
