<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('name');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_email', 'subscription_ends_at']);
        });
    }
};
