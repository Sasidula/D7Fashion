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
        Schema::create('internal_product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_product_id')->constrained()->onDelete('cascade');
            $table->foreignId('assignment_id')->constrained('material_assignments')->onDelete('cascade')->nullable();
            $table->enum('use', [ 'reviewing', 'approved', 'rejected']);
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
        Schema::dropIfExists('internal_product_items');
    }
};
