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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('points')->default(0)->after('status');
            $table->string('referral_code', 20)->unique()->nullable()->after('points');
            $table->unsignedBigInteger('referred_by_id')->nullable()->after('referral_code');
            
            $table->foreign('referred_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by_id']);
            $table->dropColumn(['points', 'referral_code', 'referred_by_id']);
        });
    }
};
