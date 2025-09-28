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
        Schema::table('barang', function (Blueprint $table) {
            // Hapus kolom Berat dan Unit
            $table->dropColumn(['Berat', 'Unit']);
            
            // Update kolom Satuan untuk menambahkan pilihan Drum dan Pil
            $table->string('Satuan', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Tambahkan kembali kolom Berat dan Unit
            $table->decimal('Berat', 8, 2)->default(1);
            $table->string('Unit', 20)->default('pcs');
            
            // Kembalikan kolom Satuan ke format sebelumnya
            $table->string('Satuan', 10)->change();
        });
    }
};
