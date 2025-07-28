<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjadwalan', function (Blueprint $table) {
            // Tambah kolom jika belum ada
            if (!Schema::hasColumn('penjadwalan', 'Tanggal_Mulai')) {
                $table->dateTime('Tanggal_Mulai')->nullable();
            }

            if (!Schema::hasColumn('penjadwalan', 'Tanggal_Selesai')) {
                $table->dateTime('Tanggal_Selesai')->nullable();
            }

            if (!Schema::hasColumn('penjadwalan', 'Status')) {
                $table->enum('Status', ['Menunggu', 'Berjalan', 'Selesai'])->default('Menunggu');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjadwalan', function (Blueprint $table) {
            $table->dropColumn(['Tanggal_Mulai', 'Tanggal_Selesai', 'Status']);
        });
    }
};

