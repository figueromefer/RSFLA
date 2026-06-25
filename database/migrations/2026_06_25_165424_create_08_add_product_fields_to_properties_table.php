<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('state');
            $table->string('report_title')->nullable()->after('hero_image');
            $table->boolean('is_active')->default(true)->after('status');

            $table->index(['is_active', 'state']);
        });

        DB::table('properties')->update([
            'is_active' => DB::raw("status = 'active'"),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'state']);
            $table->dropColumn(['hero_image', 'report_title', 'is_active']);
        });
    }
};
