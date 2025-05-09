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
        Schema::create('ruangankelas', function (Blueprint $table) {
            $table->id();
            $table->string("nama_ruangan", 100);
            $table->boolean("status");
            $table->string("kd_jurusan", 100);
            $table->foreign('kd_jurusan')->references('kd_jurusan')->on('jurusans');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangankelas');
    }
};
