<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemReturnResource extends JsonResource
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
            'return_no'=>$this->return_no,
            'qty'=>$this->qty,
            'return_date'=>$this->return_date,
            'comments'=>$this->comments,
            'itemReturnDetails'=>ItemReturnDetailResourceCollection::make($this->whenLoaded('itemReturnDetails')),
        ];
    }
}
