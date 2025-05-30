<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('agendaupacara', function (Blueprint $table) {
            $table->string('kd_agendaupacara')->primary();
            $table->date('tanggal_upacara');
            $table->string('id_status_upacara', 50);
            $table->unsignedBigInteger("id_hari");
            $table->foreign('id_hari')
                ->references('id')
                ->on('hari');
            $table->foreign('id_status_upacara')
                ->references('id_status_upacara')
                ->on('statusagendaupacara');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendaupacara');
    }
};
