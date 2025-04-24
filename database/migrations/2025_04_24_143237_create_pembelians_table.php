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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->string('Nota')->primary();
            $table->date('TglNota');
            $table->string('KdSuplier');
            $table->decimal('Diskon', 5, 2);
            $table->timestamps();

            $table->foreign('KdSuplier')->references('KdSuplier')->on('supliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
