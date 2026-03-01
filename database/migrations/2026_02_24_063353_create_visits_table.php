<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entrance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('space_id')->nullable()->constrained('spaces')->nullOnDelete();
            $table->foreignId('host_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('host_name')->nullable();
            $table->string('host_email')->nullable();
            $table->string('purpose')->nullable();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->enum('status', ['pending', 'checked_in', 'checked_out'])->default('pending');
            $table->string('qr_code')->unique()->nullable();
            $table->string('check_in_code', 6)->nullable()->unique();
            $table->timestamp('gdpr_consent_at')->nullable();
            $table->timestamp('nda_consent_at')->nullable();
            $table->text('signature')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('check_in_at');
            $table->index('qr_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};