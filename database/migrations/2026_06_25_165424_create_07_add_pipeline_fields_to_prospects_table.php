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
        Schema::table('prospects', function (Blueprint $table) {
            $table->string('suite')->nullable()->after('assigned_team_member_id');
            $table->string('tenant')->nullable()->after('suite');
            $table->string('use_type')->nullable()->after('tenant');
            $table->string('timing')->nullable()->after('use_type');
            $table->unsignedInteger('rsf')->nullable()->after('timing');
            $table->string('broker')->nullable()->after('rsf');
            $table->string('contact_name')->nullable()->after('broker');
            $table->boolean('visible_to_client')->default(true)->after('notes');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('visible_to_client');

            $table->index(['property_id', 'visible_to_client', 'sort_order']);
            $table->index(['tenant', 'broker']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropIndex(['property_id', 'visible_to_client', 'sort_order']);
            $table->dropIndex(['tenant', 'broker']);
            $table->dropColumn([
                'suite',
                'tenant',
                'use_type',
                'timing',
                'rsf',
                'broker',
                'contact_name',
                'visible_to_client',
                'sort_order',
            ]);
        });
    }
};
