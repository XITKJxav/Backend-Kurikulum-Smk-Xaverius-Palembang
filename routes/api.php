<?php

use App\Http\Controllers\AgendaUpacara\AgendaUpacaraController;
use App\Http\Controllers\FileUpload\FileUploadController;
use App\Http\Controllers\Jadwal\JadwalController;
use App\Http\Controllers\JamPembelajaran\JamPembelajaranController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Jurusan\JurusanController;
use App\Http\Controllers\Karyawan\KaryawanController;
use App\Http\Controllers\MataPelajaran\MataPelajaranController;
use App\Http\Controllers\Murid\MuridController;
use App\Http\Controllers\Pengajar\PengajarController;
use App\Http\Controllers\Role\RoleController;
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
    Route::get('/role', [RoleController::class, "getRole"]);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('siswa/signout', [MuridController::class, 'logout']);
        Route::post('karyawan/signout', [KaryawanController::class, 'logout']);
        // Route::apiResource("siswa", MuridController::class);
    });
    Route::apiResource("siswa", MuridController::class);

    Route::get('status-agenda-upacara', [AgendaUpacaraController::class, 'fetchStatusUpacara'])->name('agenda');
    Route::get('waktu-upacara', [JamPembelajaranController::class, 'printTimeWithUpacara'])->name('printTimeWithUpacara');
    Route::get('waktu-regular', [JamPembelajaranController::class, 'printTime'])->name('reguler');
    Route::get('hari', [JadwalController::class, 'getDay'])->name('day');
    Route::get('jadwal', [JadwalController::class, 'getJadwal'])->name('jadwal');
    Route::post('jadwal', [JadwalController::class, 'createJadwal']);
    Route::put('jadwal', [JadwalController::class, 'updateJadwal']);
    Route::get('durasi-pembelajaran', [JadwalController::class, 'fetchDurationTimeStudy']);
    Route::put('durasi-pembelajaran', [JadwalController::class, 'updateDurationTimeStudy']);

    Route::apiResource('ruang-kelas', RuangKelasController::class);
    Route::apiResource("karyawan", KaryawanController::class);
    Route::apiResource('file-uploads', FileUploadController::class);
    Route::apiResource('jurusan', JurusanController::class);
    Route::apiResource('agenda-upacara', AgendaUpacaraController::class);
    Route::apiResource('mata-pelajaran', MataPelajaranController::class);
    Route::apiResource('pengajar', PengajarController::class);

    //->middleware(['auth:sanctum', 'ability:access-api']);
});
