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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // Mã hóa đơn (VD: HD-12345)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('room_name');
            $table->decimal('total_price', 15, 2); // Tổng tiền
            $table->decimal('deposit_amount', 15, 2); // Tiền cọc (30%)
            $table->string('payment_status')->default('deposited'); // Trạng thái: cọc hoặc đã thanh toán đủ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};