<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rent_roll_entries', function (Blueprint $table) {
            $table->string('tenant_name')->nullable()->change();
            $table->date('lease_commencement_date')->nullable()->after('square_footage');
            $table->string('lease_term')->nullable()->after('lease_expiration_date');
            $table->string('start_rent')->nullable()->after('lease_term');
            $table->string('rent_increases')->nullable()->after('start_rent');
            $table->string('free_rent')->nullable()->after('rent_increases');
            $table->boolean('is_vacant')->default(false)->after('free_rent');
        });
    }

    public function down(): void
    {
        Schema::table('rent_roll_entries', function (Blueprint $table) {
            $table->dropColumn(['lease_commencement_date', 'lease_term', 'start_rent', 'rent_increases', 'free_rent', 'is_vacant']);
            $table->string('tenant_name')->nullable(false)->change();
        });
    }
};
