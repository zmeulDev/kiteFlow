<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('billing_address')->nullable()->after('contact_phone');
            $table->string('vat_id')->nullable()->after('billing_address');
            $table->text('contract_notes')->nullable()->after('vat_id');
            $table->decimal('monthly_rate', 10, 2)->default(0)->after('contract_notes');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['contact_phone', 'billing_address', 'vat_id', 'contract_notes', 'monthly_rate']);
        });
    }
};
