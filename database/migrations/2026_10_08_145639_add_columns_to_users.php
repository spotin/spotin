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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->boolean('is_admin')->default(false)->after('password');
            // The checklist of the first steps on the dashboard, until the user hides it or does them all
            $table->boolean('show_getting_started')->default(true)->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
            $table->dropColumn('is_admin');
            $table->dropColumn('show_getting_started');
        });
    }
};
