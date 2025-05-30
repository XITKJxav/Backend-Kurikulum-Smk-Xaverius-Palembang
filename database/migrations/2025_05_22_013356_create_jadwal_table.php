<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->string("id_mata_pelajaran")->nullable();
            $table->string("id_pengajar")->nullable();
            $table->unsignedBigInteger("kd_jam_pembelajaran");
            $table->unsignedBigInteger("id_hari");
            $table->string("kd_guru_piket")->nullable();
            $table->unsignedBigInteger("id_ruangan_kelas")->nullable();
            $table->primary(['kd_jam_pembelajaran', 'id_hari', 'id_ruangan_kelas']);

            $table->foreign('kd_jam_pembelajaran')
                ->references('id')
                ->on('jam_belajar');
            $table->foreign('id_hari')
                ->references('id')
                ->on('hari');
            $table->foreign('kd_guru_piket')
                ->references('kd_karyawan')
                ->on('karyawan');
            $table->foreign('id_mata_pelajaran')
                ->references('id_mata_pelajaran')
                ->on('mata_pelajaran');
            $table->foreign('id_pengajar')
                ->references('kd_karyawan')
                ->on('karyawan');
            $table->foreign('id_ruangan_kelas')
                ->references('id')
                ->on('ruangankelas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
