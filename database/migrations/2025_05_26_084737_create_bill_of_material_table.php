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
        Schema::create('bill_of_material', function (Blueprint $table) {
            $table->id('Id_bill_of_material'); // Primary Key
            $table->string('Nama_bill_of_material'); // Nama BOM
            $table->enum('Status', ['draft', 'approved', 'rejected'])->default('draft'); // Status
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_material');
    }
};
