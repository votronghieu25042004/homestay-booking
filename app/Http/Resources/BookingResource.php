<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'room_name' => $this->room_name,
            
            // Khớp với các field frontend mong đợi
            'total_amount' => (float) ($this->total_price ?? $this->total_amount),
            'paid_amount' => (float) ($this->deposit_amount ?? $this->paid_amount),
            'total_price' => (float) $this->total_price,
            'deposit_amount' => (float) $this->deposit_amount,
            
            'check_in_date' => $this->check_in_date,
            'check_in_time' => $this->check_in_time,
            'check_out_date' => $this->check_out_date,
            'check_out_time' => $this->check_out_time,
            'status' => $this->status,
            'status_label' => $this->status_label ?? $this->getStatusLabel(),
            'source' => $this->source ?? 'website',
            'adults' => (int) $this->adults,
            'children' => (int) $this->children,
            'is_reviewed' => $this->relationLoaded('review') ? $this->review !== null : false,
            
            'customer' => $this->whenLoaded('customer', function() {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'phone' => $this->customer->phone,
                    'email' => $this->customer->email,
                ];
            }),
            
            'room' => new RoomResource($this->whenLoaded('room')),
            'services' => $this->whenLoaded('services'),
            'payments' => $this->whenLoaded('payments'),
            'activities' => $this->whenLoaded('activities'),
            
            'nights_count' => (int) $this->nights_count,
            'remaining_amount' => (float) $this->remaining_amount,
            'internal_note' => $this->internal_note,
            'guest_note' => $this->guest_note,
            'id_card_image' => $this->id_card_image,
            
            'created_at' => $this->created_at ? (is_string($this->created_at) ? $this->created_at : $this->created_at->format('d/m/Y H:i')) : null,
        ];
    }

    protected function getStatusLabel()
    {
        return match($this->status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'deposited' => 'Đã cọc',
            'checked_in' => 'Đang ở',
            'checked_out' => 'Đã trả phòng',
            'cancelled' => 'Đã hủy',
            default => $this->status
        };
    }
}
