<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produksi_id');
            $table->unsignedBigInteger('bill_of_material_id')->nullable();
            $table->unsignedBigInteger('barang_id');
            $table->integer('jumlah')->default(0);
            $table->string('status', 50)->nullable();
            $table->timestamps();

            $table->foreign('produksi_id')->references('Id_Produksi')->on('produksi')->onDelete('cascade');
            $table->foreign('barang_id')->references('Id_Bahan')->on('barang')->onDelete('cascade');
            $table->foreign('bill_of_material_id')->references('Id_bill_of_material')->on('bill_of_material')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_detail');
    }
};
