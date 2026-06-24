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
        Schema::create('asset_components', function (Blueprint $table) {
            $table->id("id_asset_component");
            $table->foreignId('asset_id')->constrained('assets', 'id_asset')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('components', 'id_component')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['asset_id', 'component_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_components');
    }
};
