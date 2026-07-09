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
        Schema::table('asset_repairs', function (Blueprint $table) {
            $table->string('hm_km')->nullable()->before('started_at');
            $table->string('poin')->nullable()->after('hm_km');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_repairs', function (Blueprint $table) {
            $table->dropColumn('hm_km');
            $table->dropColumn('poin');
        });
    }
};
