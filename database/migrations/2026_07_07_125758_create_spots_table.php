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
        Schema::create('spots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('website_target', 255)->nullable();
            $table->boolean('direct_access_to_website_target')->default(false);
            $table->boolean('configured')->default(true);
            $table->boolean('public')->default(false);
            $table->json('payload')->nullable();

            $table->timestamps(); // created_at + updated_at
            $table->softDeletes(); // deleted_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spots');
    }
};
