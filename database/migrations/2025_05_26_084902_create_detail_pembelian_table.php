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
        Schema::create('detail_pembelian', function (Blueprint $table) {
            $table->id('Id_Detail_Pembelian');
            $table->foreignId('pembelian_Id_Pembelian')->constrained('pembelian')->onDelete('cascade');
            $table->foreignId('barang_Id_Barang')->constrained('barang')->onDelete('cascade');
            $table->integer('Jumlah');
            $table->decimal('Harga_Satuan', 10, 2);
            $table->decimal('Subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian');
    }
};
