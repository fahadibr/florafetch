<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('common_name');
            $table->string('botanical_name');
            $table->text('description')->nullable();
            $table->enum('size', ['small', 'medium', 'large']);
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('sunlight_requirement', 100);
            $table->string('watering_frequency', 100);
            $table->text('soil_recommendation')->nullable();
            $table->decimal('temperature_min_c', 5, 1)->nullable();
            $table->decimal('temperature_max_c', 5, 1)->nullable();
            $table->boolean('is_low_maintenance')->default(false);
            $table->boolean('is_pet_friendly')->default(false);
            $table->enum('growth_rate', ['Slow', 'Moderate', 'Fast'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
