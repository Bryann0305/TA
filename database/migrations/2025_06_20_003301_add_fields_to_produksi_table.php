<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->id('Id_Produksi');
            $table->string('Nama_Produksi')->nullable();
            $table->integer('Jumlah_Produksi')->nullable();
            $table->string('Hasil_Produksi', 100);
            $table->enum('Status', ['Menunggu', 'Berjalan', 'Selesai']);
            $table->dateTime('Tanggal_Produksi');
            $table->longText('Keterangan');
            $table->integer('Jumlah_Berhasil');
            $table->integer('Jumlah_Gagal');
            $table->unsignedBigInteger('bahan_baku_Id_Bahan');
            $table->unsignedBigInteger('pesanan_produksi_Id_Pesanan');
            $table->unsignedBigInteger('penjadwalan_Id_Jadwal');
            $table->unsignedBigInteger('bill_of_material_Id_bill_of_material');
            $table->timestamps();

            $table->foreign('bahan_baku_Id_Bahan')->references('Id_Bahan')->on('barang');
            $table->foreign('pesanan_produksi_Id_Pesanan')->references('Id_Pesanan')->on('pesanan_produksi');
            $table->foreign('penjadwalan_Id_Jadwal')->references('Id_Jadwal')->on('penjadwalan');
            $table->foreign('bill_of_material_Id_bill_of_material')->references('Id_bill_of_material')->on('bill_of_material');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};
