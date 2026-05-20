<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL/MariaDB, we can use a raw statement to alter the enum
        DB::statement("ALTER TABLE rooms MODIFY COLUMN rent_type ENUM('whole_house', 'room_based', 'private_room', 'home') NOT NULL DEFAULT 'whole_house'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // To reverse, we'd need to handle any 'home' records first, then revert the enum
        DB::statement("ALTER TABLE rooms MODIFY COLUMN rent_type ENUM('whole_house', 'room_based', 'private_room') NOT NULL DEFAULT 'whole_house'");
    }
};
