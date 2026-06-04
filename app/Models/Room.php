<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // Tắt timestamps nếu trong DB bạn tự set DEFAULT CURRENT_TIMESTAMP chứ không dùng created_at/updated_at chuẩn của Laravel
    public $timestamps = false;

    protected $fillable = [
        'title',
        'location',
        'type',
        'rent_type',
        'parent_id',
        'price',
        'max_guests',
        'max_children',
        'description',
        'status',
        'is_visible',
        // Booking management fields
        'room_number',
        'floor',
        'room_status',
        'room_status_updated_by',
        'out_of_order_reason',
        'estimated_fix_date',
    ];

    protected $appends = ['average_rating'];

    // Khai báo mối quan hệ 1 Phòng có nhiều Ảnh
    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_hidden', false);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?: 0;
    }

    // Scope: tìm phòng trống trong khoảng ngày
    public function scopeAvailable($query, $checkIn, $checkOut)
    {
        return $query->where('room_status', '!=', 'out_of_order')
            ->whereNotIn('id', function ($sub) use ($checkIn, $checkOut) {
                $sub->select('room_id')->from('bookings')
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn);
            });
    }

    // Quan hệ: Một phòng thuộc một Homestay cha
    public function parentHomestay()
    {
        return $this->belongsTo(Room::class, 'parent_id');
    }

    // Quan hệ: Một Homestay có nhiều phòng riêng
    public function privateRooms()
    {
        return $this->hasMany(Room::class, 'parent_id');
    }

    public function childRooms()
    {
        return $this->hasMany(Room::class, 'parent_id');
    }

    // Cần ghi đè truy vấn location nếu là phòng riêng
    public function getLocationAttribute($value)
    {
        // Nếu là phòng riêng và location trống, lấy từ cha
        if ($this->rent_type === 'private_room' && (empty($value) || $value === 'null')) {
            return $this->parentHomestay ? $this->parentHomestay->location : $value;
        }
        // Đối với 'home' hoặc 'whole_house', location được lưu trực tiếp tại record này
        return $value;
    }
}