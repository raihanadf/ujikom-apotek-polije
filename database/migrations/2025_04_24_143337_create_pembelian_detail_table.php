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
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->string('Nota');
            $table->string('KdObat');
            $table->integer('Jumlah');

            $table->foreign('Nota')->references('Nota')->on('pembelians')->onDelete('cascade');
            $table->foreign('KdObat')->references('KdObat')->on('obats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_detail_tables');
    }
};
