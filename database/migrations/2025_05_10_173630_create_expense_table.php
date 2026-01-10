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
            $table->id()->primary()->comment('Id da despesa');
            $table->string('description')->comment('Descrição da despesa');
            $table->float('price')->comment('Preço da despesa');
            $table->integer('parcels')->default(1)->comment('Número de parcelas');
            $table->dateTime('payment_date')->comment('Data de pagamento');
            $table->boolean('intermediary')->default(false)->comment('Se a despesa possui intermediáros');
            $table->date('maturity')->comment('Data de vencimento');
            $table->timestamps();
            
            $table->foreignId('payer_id')->references('id')->on('users')->comment('Id do pagador da despesa');
            $table->jsonb('intermediaries')->comment('Dados dos intermediários da despesa');
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
