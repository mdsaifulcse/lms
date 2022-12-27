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
            'vendor_payment_no'=>$this->vendor_payment_no,
            'vendor_name'=>$this->vendor->name,
            'vendor_mobile'=>$this->vendor->mobile,
            'paid_amount'=>$this->paid_amount,
            'comments'=>$this->comments,
        ];
    }
}
