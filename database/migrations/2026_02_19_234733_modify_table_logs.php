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
        if (!Schema::hasColumn('logs', 'action')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->enum('action', ['create', 'update', 'delete'])->default('create')->comment('Ação do usuário realizada na base de dados');
            });
        }

        if (!Schema::hasColumn('logs', 'old_value')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->json('old_value')->nullable()->comment('Valor anterior da coluna');
            });
        }

        if (!Schema::hasColumn('logs', 'new_value')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->json('new_value')->nullable()->comment('Novo valor da coluna');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('logs', 'action')) {
            Schema::table('logs', function (Blueprint $table) {
               $table->dropColumn('action');
            });
        }

        if (Schema::hasColumn('logs', 'old_value')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->dropColumn('old_value');
            });
        }

        if (Schema::hasColumn('logs', 'new_value')) {
            Schema::table('logs', function (Blueprint $table) {
                $table->dropColumn('new_value');
            });
        }
    }
};
