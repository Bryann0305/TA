<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan_produksi', function (Blueprint $table) {
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('pesanan_produksi', 'Jumlah_Pesanan')) {
                $table->integer('Jumlah_Pesanan');
            }

            if (!Schema::hasColumn('pesanan_produksi', 'Status')) {
                $table->enum('Status', ['Menunggu', 'Diproses', 'Selesai'])->default('Menunggu');
            }

            if (!Schema::hasColumn('pesanan_produksi', 'Tanggal_Pesanan')) {
                $table->date('Tanggal_Pesanan');
            }

            if (!Schema::hasColumn('pesanan_produksi', 'user_Id_User')) {
                $table->unsignedBigInteger('user_Id_User');
            }

            if (!Schema::hasColumn('pesanan_produksi', 'pelanggan_Id_Pelanggan')) {
                $table->unsignedBigInteger('pelanggan_Id_Pelanggan');
            }

            if (!Schema::hasColumn('pesanan_produksi', 'Surat_Perintah_Produksi')) {
                $table->text('Surat_Perintah_Produksi')->nullable();
            }

            // Tambahkan foreign key opsional
            if (!Schema::hasColumn('pesanan_produksi', 'user_Id_User')) {
                $table->foreign('user_Id_User')->references('Id_User')->on('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('pesanan_produksi', 'pelanggan_Id_Pelanggan')) {
                $table->foreign('pelanggan_Id_Pelanggan')->references('Id_Pelanggan')->on('pelanggan')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_produksi', function (Blueprint $table) {
            $table->dropColumn([
                'Jumlah_Pesanan',
                'Status',
                'Tanggal_Pesanan',
                'user_Id_User',
                'pelanggan_Id_Pelanggan',
                'Surat_Perintah_Produksi',
            ]);
        });
    }
};

