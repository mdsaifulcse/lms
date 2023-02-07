<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemOrderResource extends JsonResource
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
        'order_no'=>$this->order_no,
        'qty'=>$this->qty,
        'amount'=>$this->amount,
        'tentative_date'=>$this->tentative_date,
        'vendor_id '=>$this->vendor_id,
        'vendor_name'=>$this->vendor?$this->vendor->name:'',
        'vendor_mobile'=>$this->vendor?$this->vendor->mobile:'',
        'status'=>$this->status,
        'itemOrderDetails'=>ItemOrderDetailsResourceCollection::make($this->whenLoaded('itemOrderDetails')),
    ];
    }
}
