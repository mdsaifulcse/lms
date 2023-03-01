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
            'user_id'=>$this->user_id,
            'rental_no'=>$this->rental_no,
            'rental_date'=>$this->rental_date?date('Y-m-d h:i a',strtotime($this->rental_date)):'',
            'return_date'=>$this->return_date?date('Y-m-d h:i a',strtotime($this->return_date)):'',
            'qty'=>$this->qty,
            'status'=>$this->status,
            'amount_of_penalty'=>$this->amount_of_penalty,
            'penalty_status'=>$this->penalty_status,
            'note'=>$this->note,
            'user'=>UserResource::make($this->whenLoaded('user')),
            'itemRentalDetails'=>ItemRentalDetailResourceCollection::make($this->whenLoaded('itemRentalDetails')),
        ];
    }
}
