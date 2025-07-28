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
        Schema::table('pelanggan', function (Blueprint $table) {
            if (!Schema::hasColumn('pelanggan', 'Nomor_Telp')) {
                $table->string('Nomor_Telp', 45)->nullable();
            }

            if (!Schema::hasColumn('pelanggan', 'Alamat')) {
                $table->longText('Alamat')->nullable();
            }

            if (!Schema::hasColumn('pelanggan', 'Status')) {
                $table->string('Status', 60)->default('Aktif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn(['Nomor_Telp', 'Alamat', 'Status']);
        });
    }
};
