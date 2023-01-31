<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ThirdSubCategoryResource extends JsonResource
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
            'category_id'=>$this->suCategory->category_id ,
            'sub_category_id'=>$this->sub_category_id,
            'sub_category_name'=>$this->suCategory->name ,
            'name'=>$this->name,
            'description'=>$this->description,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
        ];
    }
}
