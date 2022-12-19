<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'name'=>$this->name,
            'email'=>$this->email,
            'mobile'=>$this->mobile,
            'total_due'=>$this->total_due,
            'balance'=>$this->balance,
            'contact_person'=>$this->contact_person,
            'contact_person_mobile'=>$this->contact_person_mobile,
            'office_address'=>$this->office_address,
            'warehouse_address'=>$this->warehouse_address,
            'primary_supply_products'=>$this->primary_supply_products,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
            'photo'=>$this->photo?url($this->photo):'',
        ];
    }
}
