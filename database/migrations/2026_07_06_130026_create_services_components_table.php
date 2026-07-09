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
        Schema::create('services_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_repair_id')->constrained('asset_repairs', 'id_asset_repair')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('components', 'id_component')->onDelete('cascade');
            $table->string('merk')->nullable();
            $table->string('qty');
            $table->string('price');
            $table->string('date')->nullable();
            $table->string('store')->nullable();
            $table->string('note')->nullable();
            $table->string('technician')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_components');
    }
};
