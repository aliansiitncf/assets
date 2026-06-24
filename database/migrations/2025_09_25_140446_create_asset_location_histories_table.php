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
        Schema::create('asset_location_histories', function (Blueprint $table) {
            $table->id('id_asset_location_history');
            $table->foreignId('asset_id')->constrained('assets', 'id_asset')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations', 'id_location')->onDelete('cascade');
            $table->string('details');
            $table->timestamp('moved_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_location_histories');
    }
};
