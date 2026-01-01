<?php

namespace App\Http\Resources\Api\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,

            // Identity
            'role'        => $this->role,
            'full_name'  => $this->full_name,

            // Profile
            'gender'        => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'language'      => $this->language,
            'avatar'        => $this->avatar,
            'avatar_url'    => $this->avatar_url,

            // Contact & auth (no tokens exposed)
            'email'              => $this->email,

            // App state
            'status'                           => $this->status,

            // Timestamps
            'created_at' => $this->created_at,
        ];
    }
}
