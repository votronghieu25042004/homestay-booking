<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    public $timestamps = false;
    protected $fillable = ['room_id', 'image_url', 'is_primary'];
}