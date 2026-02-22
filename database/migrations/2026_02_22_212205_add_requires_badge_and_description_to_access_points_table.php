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
        Schema::table('access_points', function (Blueprint $table) {
            $table->boolean('requires_badge')->default(false)->after('settings');
            $table->text('description')->nullable()->after('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('access_points', function (Blueprint $table) {
            $table->dropColumn(['requires_badge', 'description']);
        });
    }
};
