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
        Schema::table('visitors', function (Blueprint $table) {
            $table->boolean('is_flagged')->default(false)->after('phone');
            $table->boolean('is_vip')->default(false)->after('is_flagged');
            $table->text('internal_notes')->nullable()->after('is_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['is_flagged', 'is_vip', 'internal_notes']);
        });
    }
};
