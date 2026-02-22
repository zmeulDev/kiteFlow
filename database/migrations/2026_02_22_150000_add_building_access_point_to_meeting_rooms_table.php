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
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->after('tenant_id')->constrained()->onDelete('set null');
            $table->foreignId('access_point_id')->nullable()->after('building_id')->constrained()->onDelete('set null');

            $table->index(['building_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->dropForeign(['access_point_id']);
            $table->dropForeign(['building_id']);
            $table->dropColumn(['access_point_id', 'building_id']);
            $table->dropIndex(['building_id', 'is_active']);
        });
    }
};