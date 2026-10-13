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
        Schema::create('parking_spots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('price_per_hour', 8, 2);
            $table->integer('capacity')->default(1);
            $table->boolean('open_24_7')->default(false);
            $table->boolean('has_video')->default(false);
            $table->boolean('accepts_card')->default(false);
            $table->integer('distance_from_center')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('reviews_count')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('is_active');
            $table->index(['lat', 'lng']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_spots');
    }
};
