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
        Schema::table('spots', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->decimal('latitude', 9, 6)->after('description');
            $table->decimal('longitude', 9, 6)->after('latitude');
            $table->string('website', 2048)->nullable()->after('longitude');
            $table->boolean('redirect_to_website')->default(false)->after('website');
            $table->boolean('is_hidden')->default(false)->after('redirect_to_website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spots', function (Blueprint $table) {
            $table->dropColumn(['description', 'latitude', 'longitude', 'website', 'redirect_to_website', 'is_hidden']);
        });
    }
};
