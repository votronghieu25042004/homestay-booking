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
            // Create correctly without DB-level foreign key to avoid type mismatch issues
            if (!Schema::hasColumn('rooms', 'parent_id')) {
                $table->integer('parent_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('rooms', 'rent_type')) {
                $table->enum('rent_type', ['whole_house', 'room_based', 'private_room'])->default('whole_house')->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            try {
                $table->dropForeign(['parent_id']);
            } catch (\Exception $e) {}
            
            $table->dropColumn(['parent_id', 'rent_type']);
        });
    }
};
