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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price');
            $table->integer('parcels')->default(1);
            $table->dateTime('datePayment');
            $table->boolean('intermediary')->default(false);
            $table->date('maturity');
            $table->timestamps();
            
            $table->foreignId('payee_id')->references('id')->on('users');
            $table->jsonb('intermediarys_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense');
    }
};
