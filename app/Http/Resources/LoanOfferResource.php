<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanOfferResource extends JsonResource
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
            'interest_rate' => $this->interest_rate,
            'maturity_date' => $this->maturity_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'loan' => $this->loans ? [
                'id' => $this->loans->id,
                'reason' => $this->loans->reason,
                'borrower' => $this->loans->borrower->name
            ] : null,
        ];
    }
}
