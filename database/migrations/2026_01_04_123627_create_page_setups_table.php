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
        Schema::create('page_setups', function (Blueprint $table) {
            $table->id('id_page_setup');
            $table->string('size_name');
            $table->integer('width');          // mm
            $table->integer('height');         // mm
            $table->integer('column')->default(1);
            $table->integer('gap_horizontal')->default(0); // mm
            $table->integer('gap_vertical')->default(0);   // mm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_setups');
    }
};
