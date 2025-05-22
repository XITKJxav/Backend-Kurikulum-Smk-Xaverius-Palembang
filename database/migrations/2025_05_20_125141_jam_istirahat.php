<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('jam_istirahat', function (Blueprint $table) {
            $table->id();
            $table->integer('durasi');
            // $table->time('jam_mulai');
            // $table->time('jam_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_istirahat');
    }
};
