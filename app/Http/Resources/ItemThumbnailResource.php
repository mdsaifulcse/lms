<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemThumbnailResource extends JsonResource
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
          'big'=>$this->big?url($this->big):null,
          'medium'=>$this->medium?url($this->medium):null,
          'small'=>$this->small?url($this->small):null,
        ];
    }
}
