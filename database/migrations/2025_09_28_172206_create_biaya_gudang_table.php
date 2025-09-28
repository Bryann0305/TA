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
        Schema::create('biaya_gudang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gudang_Id_Gudang');
            $table->decimal('biaya_sewa', 15, 2)->default(0);
            $table->decimal('biaya_listrik', 15, 2)->default(0);
            $table->decimal('biaya_air', 15, 2)->default(0);
            $table->date('tanggal_biaya');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('gudang_Id_Gudang')->references('Id_Gudang')->on('gudang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_gudang');
    }
};
