<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tạo bảng phòng
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->string('type');
            $table->decimal('price', 10, 2);
            $table->integer('max_guests')->default(2);
            $table->string('status')->default('available');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        // 2. Tạo bảng ảnh phòng
        Schema::create('room_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->string('image_url');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 3. Tạo bảng danh sách tiện nghi
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // 4. Tạo bảng nối phòng với tiện nghi
        Schema::create('room_amenities', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained('amenities')->onDelete('cascade');
            $table->primary(['room_id', 'amenity_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_amenities');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('room_images');
        Schema::dropIfExists('rooms');
    }
};