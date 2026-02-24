<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kiosk_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrance_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('welcome_message')->default('Welcome! Please check in below.');
            $table->string('logo_path')->nullable();
            $table->string('background_color')->default('#ffffff');
            $table->string('primary_color')->default('#3b82f6');
            $table->boolean('require_photo')->default(false);
            $table->boolean('require_signature')->default(true);
            $table->boolean('show_nda')->default(false);
            $table->text('gdpr_text')->nullable();
            $table->text('nda_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_settings');
    }
};