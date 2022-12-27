<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemRentalResource extends JsonResource
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
            'id'=>$this->id,
            'rental_no'=>$this->rental_no,
            'rental_date'=>$this->rental_date,
            'return_date'=>$this->return_date,
            'qty'=>$this->qty,
            'status'=>$this->status,
            'amount_of_penalty'=>$this->amount_of_penalty,
            'penalty_status'=>$this->penalty_status,
            'itemRentalDetails'=>ItemRentalDetailResourceCollection::make($this->whenLoaded('itemRentalDetails')),
        ];
    }
}
