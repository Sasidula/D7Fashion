<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            // New salary snapshot fields (for historical accuracy)
            $table->enum('salary_type', ['hourly', 'monthly', 'none'])->default('none');
            $table->decimal('salary_rate', 10, 2)->default(0.00); // rate at the time (hourly or monthly)
            $table->decimal('calculated_salary', 10, 2)->default(0.00); // final salary for that day (optional convenience)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
