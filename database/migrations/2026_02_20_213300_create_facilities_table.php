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
        // Buildings/Facilities
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->default('US');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('floors')->default(1);
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
        });

        // Zones/Areas within buildings
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type'); // floor, wing, department, restricted_area
            $table->string('floor')->nullable();
            $table->text('description')->nullable();
            $table->json('access_rules')->nullable();
            $table->boolean('requires_authorization')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['building_id', 'type']);
            $table->index(['tenant_id', 'is_active']);
        });

        // Access points / Check-in stations
        Schema::create('access_points', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('zone_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type'); // main_entrance, reception, kiosk, turnstile, elevator
            $table->string('direction')->default('both'); // entry, exit, both
            $table->string('device_id')->nullable(); // Hardware identifier
            $table->ipAddress('ip_address')->nullable();
            $table->json('settings')->nullable(); // Printer, camera, qr_scanner config
            $table->boolean('is_kiosk_mode')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
            $table->index(['device_id']);
        });

        // Access logs
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('access_point_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('subject'); // Can be User, Visitor, or Badge
            $table->string('direction'); // entry, exit
            $table->timestamp('accessed_at');
            $table->enum('result', ['granted', 'denied', 'pending'])->default('granted');
            $table->string('denial_reason')->nullable();
            $table->json('metadata')->nullable(); // Photo, temperature, etc.
            $table->timestamps();

            $table->index(['tenant_id', 'accessed_at']);
            $table->index(['access_point_id', 'accessed_at']);
        });

        // Parking management
        Schema::create('parking_spots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('building_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('number');
            $table->string('zone')->nullable();
            $table->string('type')->default('standard'); // standard, disabled, vip, reserved
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('parking_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('parking_spot_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('vehicle'); // Can be visitor_vehicle, user_vehicle
            $table->string('license_plate')->nullable();
            $table->string('vehicle_make')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->timestamp('entry_at');
            $table->timestamp('exit_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('fee', 10, 2)->nullable();
            $table->boolean('is_paid')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'entry_at']);
            $table->index(['parking_spot_id', 'entry_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_records');
        Schema::dropIfExists('parking_spots');
        Schema::dropIfExists('access_logs');
        Schema::dropIfExists('access_points');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('buildings');
    }
};