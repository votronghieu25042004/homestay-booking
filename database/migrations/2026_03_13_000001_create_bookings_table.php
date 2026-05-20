<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'customer_id')) {
                    $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('cascade');
                }
                if (!Schema::hasColumn('bookings', 'room_id')) {
                    $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('cascade');
                }
                if (!Schema::hasColumn('bookings', 'check_in_date')) {
                    $table->date('check_in_date')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'check_out_date')) {
                    $table->date('check_out_date')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'check_in_time')) {
                    $table->time('check_in_time')->default('14:00:00');
                }
                if (!Schema::hasColumn('bookings', 'check_out_time')) {
                    $table->time('check_out_time')->default('12:00:00');
                }
                if (!Schema::hasColumn('bookings', 'adults')) {
                    $table->unsignedTinyInteger('adults')->default(1);
                }
                if (!Schema::hasColumn('bookings', 'children')) {
                    $table->unsignedTinyInteger('children')->default(0);
                }
                if (!Schema::hasColumn('bookings', 'status')) {
                    $table->enum('status', ['pending','confirmed','checked_in','checked_out','cancelled','no_show'])->default('pending');
                }
                if (!Schema::hasColumn('bookings', 'source')) {
                    $table->enum('source', ['website','booking_com','agoda','walkin','phone','other'])->default('website');
                }
                if (!Schema::hasColumn('bookings', 'subtotal')) {
                    $table->decimal('subtotal', 15, 0)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'discount_amount')) {
                    $table->decimal('discount_amount', 15, 0)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'discount_type')) {
                    $table->enum('discount_type', ['percent','fixed'])->nullable();
                }
                if (!Schema::hasColumn('bookings', 'discount_reason')) {
                    $table->string('discount_reason')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'total_amount')) {
                    $table->decimal('total_amount', 15, 0)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'paid_amount')) {
                    $table->decimal('paid_amount', 15, 0)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'guest_note')) {
                    $table->text('guest_note')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'internal_note')) {
                    $table->text('internal_note')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('bookings', 'confirmed_by')) {
                    $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('bookings', 'checked_in_by')) {
                    $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('bookings', 'checked_out_by')) {
                    $table->foreignId('checked_out_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('bookings', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'cancel_reason')) {
                    $table->string('cancel_reason')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'refund_amount')) {
                    $table->decimal('refund_amount', 15, 0)->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        // Don't drop entire table, maybe just drop columns if necessary
    }
};
