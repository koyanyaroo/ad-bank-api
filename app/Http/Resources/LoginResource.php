<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource['token'],
            'user' => new UserResource($this->resource['user']),
        ];
    }

    /**
     * Add envelope fields around the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function with($request): array
    {
        return [
            'success' => true,
            'message' => 'Login successful',
        ];
    }
}
