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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->comment('Id da notificação');
            $table->string('type')->comment('Tipo da notificação');
            $table->morphs('notifiable');
            $table->text('data')->comment('Dados da notificação');
            $table->timestamp('read_at')->nullable()->comment('Data de leitura da notificação');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
