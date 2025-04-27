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
        Schema::table('obats', function (Blueprint $table) {
            $table->timestamp('TglKadaluarsa')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
            $table->string('KdObat')->primary();
     */
    public function down(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            Schema::dropColumns('tglKadaluarsa');
        });
    }
};
