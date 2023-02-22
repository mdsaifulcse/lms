<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorPaymentResource extends JsonResource
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
            'itemReceiveId'=>$this->item_receive_id,
            'vendor_payment_no'=>$this->vendor_payment_no,
            'vendor_name'=>$this->vendor->name,
            'vendor_mobile'=>$this->vendor->mobile,
            'payment_date'=>$this->payment_date,
            'paid_amount'=>$this->paid_amount,
            'receive_no'=>$this->itemReceive->receive_no,
            'payable_amount'=>$this->itemReceive->payable_amount,
            'total_paid_amount'=>$this->itemReceive->paid_amount,
            'item_order_id'=>$this->itemReceive->item_order_id,
            'comments'=>$this->comments,
        ];
    }
}
