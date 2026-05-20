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
        Schema::table('rooms', function (Blueprint $table) {
            $table->index('status');
            $table->index('rent_type');
            $table->index('parent_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('status');
            $table->index('check_in_date');
        });
        
        // Note: bookings.customer_id and vouchers.code are already indexed via foreignId/unique
        // reviews.user_id and reviews.room_id are also already indexed
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['rent_type']);
            $table->dropIndex(['parent_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['check_in_date']);
        });
    }
};
