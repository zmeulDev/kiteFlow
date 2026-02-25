<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('check_in_code', 6)->nullable()->unique()->after('qr_code');
            $table->timestamp('scheduled_at')->nullable()->after('check_out_at');
        });

        // Make host_name nullable (was previously required)
        Schema::table('visits', function (Blueprint $table) {
            $table->string('host_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropUnique(['check_in_code']);
            $table->dropColumn(['check_in_code', 'scheduled_at']);
        });

        // Note: Cannot reliably revert host_name to not nullable without data checks
    }
};