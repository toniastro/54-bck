<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'email' => $this->email,
          'role' => $this->role ? $this->role->name : null,
          'created_at' => $this->created_at,
          'wallet' => $this->wallet ? [
              'total_balance' => $this->wallet->total_balance,
              'available_balance' => $this->wallet->available_balance,
          ]  : null,
        ];
    }
}
