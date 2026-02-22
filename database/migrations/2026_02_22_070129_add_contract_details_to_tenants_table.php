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
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('subscription_plan', ['starter', 'professional', 'enterprise'])->nullable();
            $table->enum('billing_cycle', ['monthly', 'yearly'])->nullable();
            $table->decimal('monthly_price', 8, 2)->nullable();
            $table->decimal('yearly_price', 8, 2)->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->enum('payment_status', ['current', 'overdue', 'cancelled'])->default('current');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan',
                'billing_cycle',
                'monthly_price',
                'yearly_price',
                'contract_start_date',
                'contract_end_date',
                'payment_status',
                'notes',
            ]);
        });
    }
};
