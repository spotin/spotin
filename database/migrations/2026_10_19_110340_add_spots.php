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
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // Empty until set up, for a spot set up on site
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('website', 2048)->nullable();
            $table->boolean('redirect_to_website')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->boolean('needs_setup')->default(false);
            // When a QR code of the spot was first downloaded, to print it
            $table->timestamp('qr_code_downloaded_at')->nullable();
            $table->timestamps();
            // Kept when deleted, for its QR code to tell it is no longer in use, and to restore it. Its name is unique in its
            // organization among the spots that are not deleted, as the validation checks.
            $table->softDeletes();
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
