<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = ['booking_id','amount','payment_method','payment_type','reference_code','note','recorded_by'];
    public function booking()    { return $this->belongsTo(Booking::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
