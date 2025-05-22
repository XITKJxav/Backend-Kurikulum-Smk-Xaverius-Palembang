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
        Schema::create('pengajar', function (Blueprint $table) {
            $table->string("id_pengajar")->primary();
            $table->string("kd_karyawan");
            $table->string("id_mata_pelajaran");
            $table->boolean("status");
            $table->foreign('kd_karyawan')
                ->references('kd_karyawan')
                ->on('karyawan');
            $table->foreign('id_mata_pelajaran')
                ->references('id_mata_pelajaran')
                ->on('mata_pelajaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajar');
    }
};
