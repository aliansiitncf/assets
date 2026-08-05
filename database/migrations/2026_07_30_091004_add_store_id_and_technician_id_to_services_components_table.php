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
        Schema::table('services_components', function (Blueprint $table) {
            // hapus kolom lama kalau sebelumnya string
            $table->dropColumn(['store', 'technician']);

            $table->foreignId('vendor_id')->nullable()->after('date')->constrained('vendors')->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->after('vendor_id')->constrained('vendors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services_components', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['technician_id']);
            $table->dropColumn(['vendor_id', 'technician_id']);

            // kembalikan kolom lama
            $table->string('store')->nullable()->after('date');
            $table->string('technician')->nullable()->after('note');
        });
    }
};
