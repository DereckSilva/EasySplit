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
        if (Schema::hasColumn('expenses', 'price')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->renameColumn('price', 'price_total');
            });
        }

        if (!Schema::hasColumn('expenses', 'receive_notification')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->boolean('receive_notification')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'price_total')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->renameColumn('price_total', 'price');
            });
        }

        if (Schema::hasColumn('expenses', 'receive_notification')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('receive_notification');
            });
        }
    }
};
