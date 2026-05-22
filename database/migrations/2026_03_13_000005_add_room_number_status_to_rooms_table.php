<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'room_number')) {
                $table->string('room_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('rooms', 'floor')) {
                $table->string('floor')->nullable()->after('room_number');
            }
            if (!Schema::hasColumn('rooms', 'room_status')) {
                $table->enum('room_status', ['available','occupied','dirty','out_of_order'])->default('available')->after('status');
            }
            if (!Schema::hasColumn('rooms', 'room_status_updated_by')) {
                $table->unsignedBigInteger('room_status_updated_by')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'out_of_order_reason')) {
                $table->string('out_of_order_reason')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'estimated_fix_date')) {
                $table->date('estimated_fix_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_status_updated_by']);
            $table->dropColumn(['room_number','floor','room_status','room_status_updated_by','out_of_order_reason','estimated_fix_date']);
        });
    }
};
