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
        Schema::table('companies', function (Blueprint $table) {
            $table->date('contract_start_date')->nullable()->after('contact_person');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->foreignId('main_contact_user_id')->nullable()->after('contract_end_date')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['main_contact_user_id']);
            $table->dropColumn(['contract_start_date', 'contract_end_date', 'main_contact_user_id']);
        });
    }
};
