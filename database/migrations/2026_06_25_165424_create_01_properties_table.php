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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('market')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city');
            $table->string('state', 2);
            $table->string('postal_code', 20)->nullable();
            $table->string('property_type')->default('student_housing');
            $table->unsignedSmallInteger('unit_count')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
