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
                $table->renameColumn('price', 'priceTotal');
            });
        }

        if (!Schema::hasColumn('expenses', 'receiveNotification')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->boolean('receiveNotification')->default(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'priceTotal')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->renameColumn('priceTotal', 'price');
            });
        }

        if (Schema::hasColumn('expenses', 'receiveNotification')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('receiveNotification');
            });
        }
    }
};
