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
        Schema::create('product_sales_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_sales_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->enum('product_type', ['internal', 'external']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales_items');
    }
};
