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
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('dre')->nullable()->after('name');
            $table->string('bio_url')->nullable()->after('phone');
            $table->string('photo')->nullable()->after('bio_url');

            $table->index(['is_active', 'name']);
            $table->index('dre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'name']);
            $table->dropIndex(['dre']);
            $table->dropColumn(['dre', 'bio_url', 'photo']);
        });
    }
};
