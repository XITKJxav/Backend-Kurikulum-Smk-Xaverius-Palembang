<?php

use App\Http\Controllers\AgendaUpacara\AgendaUpacaraController;
use App\Http\Controllers\FileUpload\FileUploadController;
use App\Http\Controllers\Jadwal\JadwalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Jurusan\JurusanController;
use App\Http\Controllers\Karyawan\KaryawanController;
use App\Http\Controllers\MataPelajaran\MataPelajaranController;
use App\Http\Controllers\Murid\MuridController;
use App\Http\Controllers\RuangKelas\RuangKelasController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::post('siswa/signin', [MuridController::class, 'login'])->name('login');
    Route::post('siswa/signup', [MuridController::class, 'register']);
    Route::post('siswa/send-code', [MuridController::class, 'requestCode']);
    Route::post('siswa/reset-password', [MuridController::class, 'resetWithCode']);
    Route::post('siswa/refresh-token', [MuridController::class, 'refreshToken']);

    Route::post('karyawan/signin', [KaryawanController::class, 'login'])->name('login');
    Route::post('karyawan/signup', [KaryawanController::class, 'register']);
    Route::post('karyawan/send-code', [KaryawanController::class, 'requestCode']);
    Route::post('karyawan/reset-password/ketua-kelas', [KaryawanController::class, 'resetWithCode']);
    Route::post('karyawan/refresh-token/ketua-kelas', [KaryawanController::class, 'refreshToken']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('siswa/signout', [MuridController::class, 'logout']);
    });

    Route::get('status-agenda-upacara', [AgendaUpacaraController::class, 'printTimeWithUpacara'])->name('agenda');
    Route::get('jadwal-upacara', [JadwalController::class, 'printTimeWithUpacara'])->name('printTimeWithUpacara');
    Route::get('jadwal-regular', [JadwalController::class, 'printTime'])->name('jadwal');

    Route::apiResource('ruang-kelas', RuangKelasController::class);
    Route::apiResource("ketua-kelas", MuridController::class);
    Route::apiResource("karyawan", KaryawanController::class);
    Route::apiResource('file-uploads', FileUploadController::class);
    Route::apiResource('jurusan', JurusanController::class);
    Route::apiResource('agenda-upacara', AgendaUpacaraController::class);
    Route::apiResource('mata-pelajaran', MataPelajaranController::class);
    Route::apiResource('pengajar', PengajarController::class);
    //->middleware(['auth:sanctum', 'ability:access-api']);
});
