<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemReceiveDetailsResource extends JsonResource
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
            'name'=>$this->item->title,
            'itemId'=>$this->item_id,
            'itemQty'=>$this->item_qty,
            'itemPrice'=>$this->item_price,
            'itemTotalPrice'=>$this->item_price*$this->item_qty,
        ];
    }
}
