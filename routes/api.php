<?php

use App\Http\Controllers\FileUpload\FileUploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Jurusan\JurusanController;
use App\Http\Controllers\Karyawan\KaryawanController;
use App\Http\Controllers\Murid\MuridController;
use App\Http\Controllers\RuangKelas\RuangKelasController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::post('signin/ketua-kelas', [MuridController::class, 'login'])->name('login');
    Route::post('signup/ketua-kelas', [MuridController::class, 'register']);
    Route::post('send-code/ketua-kelas', [MuridController::class, 'requestCode']);
    Route::post('reset-password/ketua-kelas', [MuridController::class, 'resetWithCode']);
    Route::post('refresh-token/ketua-kelas', [MuridController::class, 'refreshToken']);

    Route::post('karyawan/signin', [KaryawanController::class, 'login'])->name('login');
    Route::post('karyawan/signup', [KaryawanController::class, 'register']);
    Route::post('karyawan/send-code', [KaryawanController::class, 'requestCode']);
    Route::post('karyawan/reset-password/ketua-kelas', [KaryawanController::class, 'resetWithCode']);
    Route::post('karyawan/refresh-token/ketua-kelas', [KaryawanController::class, 'refreshToken']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('signout/ketua-kelas', [KaryawanController::class, 'logout']);
    });

    Route::apiResource('ruang-kelas', RuangKelasController::class);
    Route::apiResource("ketua-kelas", MuridController::class);
    Route::apiResource("karyawan", KaryawanController::class);
    Route::apiResource('file-uploads', FileUploadController::class);
    Route::apiResource('jurusan', JurusanController::class);
    //->middleware(['auth:sanctum', 'ability:access-api']);
});
