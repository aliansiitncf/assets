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
        Schema::create('asset_damages', function (Blueprint $table) {
            $table->id('id_asset_damage');
            $table->foreignId('asset_id')->constrained('assets', 'id_asset')->cascadeOnDelete();
            $table->text('damage_note');
            $table->string('image_path')->nullable();
            $table->timestamp('reported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_damages');
    }
};
