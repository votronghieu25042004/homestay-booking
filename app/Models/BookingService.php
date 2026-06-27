<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    protected $fillable = ['booking_id','service_name','unit_price','quantity','total_price','is_paid','note'];
    public function booking() { return $this->belongsTo(Booking::class); }
}
