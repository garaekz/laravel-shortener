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
        Schema::create('code_generator_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('max_length')->default(4);
            $table->integer('max_attempts')->default(10);
            $table->integer('max_retries')->default(3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_generator_configurations');
    }
};
