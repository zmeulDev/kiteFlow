<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('plan')->default('free')->after('slug');
            $table->string('status')->default('active')->after('plan');
            $table->timestamp('trial_ends_at')->nullable()->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['plan', 'status', 'trial_ends_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
