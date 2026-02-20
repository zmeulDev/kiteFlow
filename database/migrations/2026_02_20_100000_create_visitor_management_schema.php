<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the default users migration we created
        Schema::dropIfExists('users');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('sub_tenants');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('meeting_rooms');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('settings');

        // TENANTS TABLE - Main company/organization
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person')->nullable();
            $table->integer('gdpr_retention_months')->default(6);
            $table->text('nda_text')->nullable();
            $table->text('terms_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // SUB_TENANTS TABLE - Departments within tenant
        Schema::create('sub_tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });

        // USERS TABLE with role-based access
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sub_tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'tenant_admin', 'user'])->default('user');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        // BUILDINGS TABLE - Physical locations
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // MEETING_ROOMS TABLE
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('capacity')->default(4);
            $table->text('amenities')->nullable(); // JSON: projector, whiteboard, video conf
            $table->string('floor')->nullable();
            $table->string('floor_plan_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // VISITORS TABLE - Reusable guest profiles
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('signature_path')->nullable();
            $table->timestamp('signature_signed_at')->nullable();
            $table->boolean('agreed_to_nda')->default(false);
            $table->boolean('agreed_to_terms')->default(false);
            $table->timestamp('last_visit_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // VISITS TABLE - Visit reservations
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_tenant_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('host_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('meeting_room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('visit_code')->unique(); // Pre-registration code
            $table->string('qr_code_path')->nullable();
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->text('purpose')->nullable();
            $table->enum('status', [
                'pre_registered', 
                'checked_in', 
                'checked_out', 
                'cancelled', 
                'no_show'
            ])->default('pre_registered');
            
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });

        // CHECK_INS TABLE - Detailed check-in/out logs
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('meeting_room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->dateTime('check_in_time');
            $table->dateTime('check_out_time')->nullable();
            $table->string('check_in_method', 50)->default('manual'); // manual, code, qr
            $table->string('check_out_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // SETTINGS TABLE - Tenant-specific configuration
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'key']);
        });

        // Add indexes for performance
        Schema::table('users', function (Blueprint $table) {
            $table->index(['tenant_id', 'role']);
            $table->index('email');
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->index(['tenant_id', 'status']);
            $table->index('visit_code');
            $table->index('scheduled_start');
            $table->index('visitor_id');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->index('visit_id');
            $table->index('check_in_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('meeting_rooms');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sub_tenants');
        Schema::dropIfExists('tenants');
    }
};
