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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('domain')->nullable()->unique();
            $table->string('logo_path')->nullable();
            $table->text('nda_text')->nullable();
            $table->integer('data_retention_days')->default(180);
            $table->string('stripe_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
