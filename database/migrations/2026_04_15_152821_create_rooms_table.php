<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // standard, deluxe, suite, family
            $table->text('description')->nullable();
            $table->integer('capacity')->default(2); // max guests
            $table->decimal('price_per_night', 12, 2);
            $table->decimal('weekend_price', 12, 2)->nullable(); // dynamic pricing
            $table->decimal('holiday_price', 12, 2)->nullable();
            $table->integer('total_rooms')->default(1);
            $table->integer('available_rooms')->default(1);
            $table->string('bed_type')->nullable(); // single, double, queen, king
            $table->integer('bed_count')->default(1);
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->boolean('has_wifi')->default(true);
            $table->boolean('has_ac')->default(true);
            $table->boolean('has_tv')->default(true);
            $table->boolean('has_bathroom')->default(true);
            $table->boolean('has_balcony')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('cover_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
