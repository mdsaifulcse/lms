<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
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
            'contact'=>$this->contact,
            'contact1'=>$this->contact1,
            'contact2'=>$this->contact2,
            'bio'=>$this->bio,
            'show_home'=>$this->show_home,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
            'photo'=>$this->photo?url($this->photo):'',
        ];
    }
}
