<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'emailVerifiedAt' => $this->email_verified_at,
            'role'            => $this->role,
            'status'          => $this->status,
            'phoneNumber'     => $this->phone_number,
            'address'         => $this->address,
            'profilePic'      => $this->profile_pic,
            'coverPic'        => $this->cover_pic,
            'createdAt'       => $this->created_at,
            'updatedAt'       => $this->updated_at,
        ];
    }
}
