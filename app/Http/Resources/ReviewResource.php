<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rating' => (int) $this->rating,
            'comment' => $this->comment,
            'is_hidden' => (bool) $this->is_hidden,
            // Khôi phục cấu trúc nested để không làm hỏng frontend
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user ? $this->user->name : 'Khách ẩn danh',
                'email' => $this->user ? $this->user->email : null,
            ],
            'room' => [
                'id' => $this->room_id,
                'title' => $this->room ? $this->room->title : 'Phòng không tồn tại',
            ],
            'created_at' => $this->created_at ? (is_string($this->created_at) ? $this->created_at : $this->created_at->toDateTimeString()) : null,
            'created_at_human' => $this->created_at ? (is_string($this->created_at) ? $this->created_at : $this->created_at->diffForHumans()) : null,
            'date' => $this->created_at ? (is_string($this->created_at) ? date('d/m/Y', strtotime($this->created_at)) : $this->created_at->format('d/m/Y')) : null,
        ];
    }
}
