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
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('check_in_code', 12)->nullable()->unique()->after('status');
            $table->foreignId('visitor_id')->nullable()->constrained()->onDelete('set null')->after('host_id');
            $table->string('visitor_name')->nullable()->after('visitor_id');
            $table->string('visitor_email')->nullable()->after('visitor_name');
            $table->string('visitor_phone')->nullable()->after('visitor_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_code',
                'visitor_id',
                'visitor_name',
                'visitor_email',
                'visitor_phone',
            ]);
        });
    }
};
