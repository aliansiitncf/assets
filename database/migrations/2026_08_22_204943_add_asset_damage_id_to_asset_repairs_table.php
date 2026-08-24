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
            $table->unsignedBigInteger('asset_damage_id')->nullable()->after('asset_id');
            $table->foreign('asset_damage_id')
                  ->references('id_asset_damage')
                  ->on('asset_damages')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_repairs', function (Blueprint $table) {
            $table->dropForeign(['asset_damage_id']);
            $table->dropColumn('asset_damage_id');
        });
    }
};
