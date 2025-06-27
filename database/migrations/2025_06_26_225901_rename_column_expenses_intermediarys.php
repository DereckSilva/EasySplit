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
        if (!Schema::hasColumn('expenses', 'intermediarys')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->renameColumn('intermediarys_id', 'intermediarys');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'intermediarys')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->renameColumn('intermediarys', 'intermediarys_id');
            });
        }
    }
};
