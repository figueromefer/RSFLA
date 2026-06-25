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
        Schema::create('marketing_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('activity_date');
            $table->string('metric_label')->nullable();
            $table->string('metric_value')->nullable();
            $table->string('url')->nullable();
            $table->boolean('visible_to_client')->default(true);
            $table->timestamps();

            $table->index(['property_id', 'visible_to_client', 'activity_date']);
            $table->index(['type', 'activity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_activities');
    }
};
