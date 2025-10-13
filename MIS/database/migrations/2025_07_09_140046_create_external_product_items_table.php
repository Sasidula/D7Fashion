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
        Schema::create('external_product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_product_id')->constrained()->onDelete('cascade');
            $table->decimal('bought_price', 10, 2); // ✅ snapshot of bought price
            $table->decimal('sold_price', 10, 2);   // ✅ snapshot of sold price
            $table->enum('status', ['available', 'sold', 'deleted'])->default('available');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_product_items');
    }
};
