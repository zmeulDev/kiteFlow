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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('id_type')->nullable(); // passport, id_card, driver_license
            $table->string('id_number')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_blacklisted')->default(false);
            $table->text('blacklist_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'email']);
            $table->index(['tenant_id', 'phone']);
            $table->index(['tenant_id', 'is_blacklisted']);
        });

        Schema::create('visitor_visits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('host_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('meeting_id')->nullable()->constrained()->onDelete('set null');
            $table->string('purpose')->nullable();
            $table->string('check_in_method')->default('reception'); // reception, kiosk, qr, app
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('badge_number')->nullable();
            $table->string('badge_type')->nullable();
            $table->enum('status', ['pre_registered', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pre_registered');
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'check_in_at']);
            $table->index(['visitor_id', 'check_in_at']);
        });

        Schema::create('visitor_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->string('type'); // id_document, nda, photo, signature
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['visitor_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_documents');
        Schema::dropIfExists('visitor_visits');
        Schema::dropIfExists('visitors');
    }
};