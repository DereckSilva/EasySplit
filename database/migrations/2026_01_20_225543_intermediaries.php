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
        Schema::create('intermediaries', function (Blueprint $table) {
            $table->id()->primary()->comment('Id do intermediário');
            $table->string('email')->unique()->comment('Email do intermediário');
            $table->string('phone_number')->unique()->comment('Número de telefone do intermediário');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intermediaries');
    }
};
