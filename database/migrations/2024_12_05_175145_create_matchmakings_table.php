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
        Schema::create('matchmakings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id_book')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('company_id_match')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('table_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('approved_company')->nullable();
            $table->integer('approved_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchmakings');
    }
};
