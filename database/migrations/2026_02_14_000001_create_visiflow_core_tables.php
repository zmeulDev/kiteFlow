<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tenants (Offices/Spaces)
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('settings')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // 2. Visitors (Reusable profiles)
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // 3. Visits (The actual event)
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table->foreignId('visitor_id')->constrained();
            $table->foreignId('user_id')->comment('The Host')->constrained();
            $table->string('purpose');
            $table->text('signature_data')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('checked_in_at')->useCurrent();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('tenants');
    }
};
