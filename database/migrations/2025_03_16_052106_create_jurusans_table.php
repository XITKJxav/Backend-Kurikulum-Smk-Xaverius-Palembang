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
        if (!Schema::hasTable('jurusans')) {
            Schema::create('jurusans', function (Blueprint $table) {
                $table->string('kd_jurusan', 100)->primary();
                $table->string('nama_jurusan', 120);
                $table->boolean('status');
                $table->timestamps();
            });
        }
    }


    public function down(): void
    {
        Schema::dropIfExists('jurusans'); // Make sure the correct table is dropped
    }
};
