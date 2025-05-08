<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\RuangKelasController;
use App\Http\Controllers\UsersPengurusKelasController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::post('signin/ketua-kelas', [UsersPengurusKelasController::class, 'login'])->name('login');
    Route::post('signup/ketua-kelas', [UsersPengurusKelasController::class, 'register']);
    Route::post('send-code/ketua-kelas', [UsersPengurusKelasController::class, 'requestCode']);
    Route::post('reset-password/ketua-kelas', [UsersPengurusKelasController::class, 'resetWithCode']);
    Route::post('refresh-token/ketua-kelas', [UsersPengurusKelasController::class, 'refreshToken']);


    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('signout/ketua-kelas', [UsersPengurusKelasController::class, 'logout']);
        // Route::apiResource("ketua-kelas", UsersPengurusKelasController::class);
    });

    Route::apiResource('ruang-kelas', RuangKelasController::class);
    Route::apiResource("ketua-kelas", UsersPengurusKelasController::class);
    Route::apiResource('file-uploads', FileUploadController::class);
    Route::apiResource('jurusan', JurusanController::class);
    //->middleware(['auth:sanctum', 'ability:access-api']);
});
