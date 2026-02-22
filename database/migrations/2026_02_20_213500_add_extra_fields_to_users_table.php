<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->default('UTC')->after('password');
            $table->string('locale', 10)->default('en')->after('timezone');
            $table->string('avatar')->nullable()->after('locale');
            $table->string('phone', 50)->nullable()->after('avatar');
            $table->string('department')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('department');
            $table->boolean('is_active')->default(true)->after('job_title');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->json('preferences')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'timezone', 'locale', 'avatar', 'phone', 
                'department', 'job_title', 'is_active', 
                'last_login_at', 'preferences'
            ]);
        });
    }
};