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
        Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('location')->nullable();
            $table->integer('capacity')->default(10);
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // projector, tv, whiteboard, etc.
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // booking rules, buffer times
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('meeting_room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('purpose')->nullable();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('timezone')->default('UTC');
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_rule')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->string('cancellation_reason')->nullable();
            $table->json('checklist')->nullable(); // pre-meeting checklist
            $table->string('meeting_url')->nullable(); // for virtual meetings
            $table->string('meeting_type')->default('in_person'); // in_person, virtual, hybrid
            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'start_at']);
            $table->index(['meeting_room_id', 'start_at']);
            $table->index(['host_id', 'start_at']);
            $table->index(['status', 'start_at']);
        });

        Schema::create('meeting_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->morphs('attendee'); // Can be User or Visitor
            $table->enum('type', ['required', 'optional'])->default('required');
            $table->enum('status', ['pending', 'accepted', 'declined', 'tentative'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['meeting_id', 'attendee_type', 'attendee_id']);
        });

        Schema::create('meeting_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->string('type'); // reminder, check_in, check_out
            $table->string('channel'); // email, sms, push
            $table->json('recipients');
            $table->timestamp('sent_at');
            $table->string('status')->default('sent');
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['meeting_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_notifications');
        Schema::dropIfExists('meeting_attendees');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('meeting_rooms');
    }
};