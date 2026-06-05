<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        // My fields
        'booking_code','customer_id','room_id',
        'check_in_date','check_out_date','check_in_time','check_out_time',
        'adults','children','status','source','id_card_image',
        'subtotal','discount_amount','discount_type','discount_reason',
        'total_amount','paid_amount',
        'guest_note','internal_note',
        'created_by','confirmed_by','checked_in_by','checked_out_by',
        'cancelled_at','cancel_reason','refund_amount',
        // Partner fields
        'customer_name', 'customer_email', 'customer_phone', 
        'room_name', 'total_price', 'deposit_amount', 'payment_status'
    ];

    protected $casts = [
        'check_in_date'  => 'date:Y-m-d',
        'check_out_date' => 'date:Y-m-d',
        'cancelled_at'   => 'datetime',
    ];

    protected $appends = ['remaining_amount', 'nights_count'];

    // ──── Boot: auto booking_code ────
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = self::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'BK-' . now()->format('Y') . strtoupper(Str::random(6));
        } while (self::where('booking_code', $code)->exists());
        return $code;
    }

    // ──── Relationships ────
    public function customer()   { return $this->belongsTo(User::class, 'customer_id'); }
    public function room()       { return $this->belongsTo(Room::class); }
    public function services()   { return $this->hasMany(BookingService::class); }
    public function payments()   { return $this->hasMany(BookingPayment::class)->latest(); }
    public function activities() { return $this->hasMany(BookingActivity::class)->latest('created_at'); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
    public function review()     { return $this->hasOne(Review::class); }

    // ──── Accessors ────
    public function getRemainingAmountAttribute(): int
    {
        return (int) max(0, (float)$this->total_amount - (float)$this->paid_amount);
    }

    public function getNightsCountAttribute(): int
    {
        return (int) \Carbon\Carbon::parse($this->check_in_date)
            ->diffInDays(\Carbon\Carbon::parse($this->check_out_date));
    }

    // ──── Scopes ────
    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopeCheckingInToday($query) { return $query->whereDate('check_in_date', today()); }
    public function scopeCheckingOutToday($query) { return $query->whereDate('check_out_date', today()); }
    public function scopeInHouse($query) { return $query->where('status', 'checked_in'); }

    // ──── Helpers ────
    public function recalculateTotals(): void
    {
        $roomTotal     = (int)($this->room->price ?? 0) * $this->nights_count;
        $servicesTotal = (int)$this->services->sum('total_price');
        $subtotal      = $roomTotal + $servicesTotal;

        $discount = (int)($this->discount_amount ?? 0);
        if ($this->discount_type === 'percent') {
            $discount = (int)round($subtotal * ($discount / 100));
        }

        $this->subtotal       = $subtotal;
        $this->discount_amount= $discount;
        $this->total_amount   = $subtotal - $discount;
        $this->save();
    }

    public function logActivity(string $type, string $desc, $old = null, $new = null): void
    {
        $user = auth('sanctum')->user();
        $this->activities()->create([
            'actor_id'    => $user?->id,
            'actor_name'  => $user?->name ?? 'System',
            'action_type' => $type,
            'old_value'   => $old,
            'new_value'   => $new,
            'description' => $desc,
        ]);
    }
}
