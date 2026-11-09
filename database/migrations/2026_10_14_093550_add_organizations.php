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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            // Unique among the organizations that are not deleted, as the validation checks: a deleted one does not keep its name
            $table->string('name');
            $table->timestamps();
            // Kept when deleted, for its QR code to tell it is no longer in use, and to restore it
            $table->softDeletes();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['responsible', 'member'])->default('member');
            $table->timestamps();

            $table->primary(['organization_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('email')->index();
            $table->enum('role', ['responsible', 'member'])->default('member');
            $table->string('token')->unique();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('organizations');
    }
};
