<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'address1'=>$this->address1,
            'address2'=>$this->address2,
            'bio'=>$this->bio,
            'show_home'=>$this->show_home,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
            'photo'=>$this->photo?url($this->photo):'',
        ];
        //return parent::toArray($request);
    }
}
