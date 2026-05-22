<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('room_id');
            $table->foreignId('booking_id')->unique()->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('1-5 stars');
            $table->text('comment')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();

            // Thiết lập khóa ngoại với rooms
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');

            // Index để truy vấn nhanh hơn
            $table->index(['room_id', 'is_hidden']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
