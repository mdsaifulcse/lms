<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'category_id'=>$this->category_id,
            'name'=>$this->name,
            'description'=>$this->description,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
            //'icon_photo'=>$this->icon_photo?url($this->icon_photo):'',
        ];
    }
}
