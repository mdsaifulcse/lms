<?php

namespace App\Http\Resources;

use App\Models\ItemReceive;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemReceiveResource extends JsonResource
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
            'item_order_id'=>$this->item_order_id,
            'vendor_id'=>$this->vendor_id,
            'qty'=>$this->qty,
            'invoice_no'=>$this->invoice_no,
            'invoice_photo'=>$this->invoice_photo?url($this->invoice_photo):null,
            'payment_status'=>$this->payment_status,
            'payable_amount'=>$this->payable_amount,
            'paid_amount'=>$this->paid_amount,
            'due_amount'=>$this->due_amount,
            'comments'=>$this->comments,
            'itemReceiveDetails'=>ItemReceiveDetailsResourceCollection::make($this->whenLoaded('itemReceiveDetails')),
        ];
    }
}
