<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingActivity extends Model
{
    public $timestamps = false;
    protected $fillable = ['booking_id','actor_id','actor_name','action_type','old_value','new_value','description'];
    protected $casts    = ['old_value'=>'array','new_value'=>'array','created_at'=>'datetime'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function actor()   { return $this->belongsTo(User::class, 'actor_id'); }
}
