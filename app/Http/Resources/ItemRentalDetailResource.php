<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemRentalDetailResource extends JsonResource
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
            'item_id'=>$this->item_id,
            'item_title'=>$this->rentalItem?$this->rentalItem->title:'',
            'item_qty'=>$this->item_qty,
            'return_date'=>$this->return_date?date('Y-m-d h:i a',strtotime($this->return_date)):'',
            'status'=>$this->status,
            'item_amount_of_penalty'=>$this->item_amount_of_penalty,
            'penalty_status'=>$this->penalty_status,
        ];
    }
}
