<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'points' => (int) $this->points,
            'referral_code' => $this->referral_code,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
