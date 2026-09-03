<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('market_rba')->nullable();
            $table->string('market_vacancy')->nullable();
            $table->string('market_sublet_percentage')->nullable();
            $table->string('market_ytd_absorption')->nullable();
            $table->longText('market_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['market_rba', 'market_vacancy', 'market_sublet_percentage', 'market_ytd_absorption', 'market_notes']);
        });
    }
};
