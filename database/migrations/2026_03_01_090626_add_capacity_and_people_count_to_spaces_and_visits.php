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
        Schema::table('spaces', function (Blueprint $table) {
            $table->integer('capacity')->nullable()->after('is_active');
        });
        
        Schema::table('visits', function (Blueprint $table) {
            $table->integer('people_count')->default(1)->after('purpose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn('people_count');
        });
    }
};
