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
        Schema::create('barang', function (Blueprint $table) {
            $table->id('Id_Bahan');
            $table->string('Nama_Bahan', 100);
            $table->integer('Stok');
            $table->enum('Jenis', ['Bahan Baku', 'Produk Jadi']);
            $table->string('Status', 60);
            $table->unsignedBigInteger('kategori_Id_Kategori');
            $table->timestamps();

            $table->foreign('kategori_Id_Kategori')->references('Id_Kategori')->on('kategori');});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
