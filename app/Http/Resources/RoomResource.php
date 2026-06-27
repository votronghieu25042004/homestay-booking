<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $primaryImage = $this->images->where('is_primary', true)->first() 
                        ?? $this->images->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'rent_type' => $this->rent_type,
            'type' => $this->type,
            'price' => (float) $this->price,
            'status' => $this->status,
            'max_guests' => $this->max_guests,
            'max_children' => $this->max_children,
            'description' => $this->description,
            'is_visible' => (bool) $this->is_visible,
            'image' => $primaryImage ? $primaryImage->image_url : null,
            'images' => RoomImageResource::collection($this->whenLoaded('images')),
            'amenities' => $this->whenLoaded('amenity_list'),
            'average_rating' => (float) $this->average_rating,
            'parent_id' => $this->parent_id,
            'parent_title' => $this->when($this->parentHomestay, function() {
                return $this->parentHomestay->title;
            }),
            'child_rooms_count' => $this->whenCounted('childRooms'),
            'child_rooms' => RoomResource::collection($this->whenLoaded('childRooms')),
            'created_at' => $this->created_at ? (is_string($this->created_at) ? $this->created_at : $this->created_at->format('Y-m-d H:i:s')) : null,
        ];
    }
}
