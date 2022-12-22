<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipPlanResource extends JsonResource
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
          'valid_duration'=>$this->valid_duration,
          'fee_amount'=>$this->fee_amount,
          'description'=>$this->description,
          'term_policy'=>$this->term_policy,
          'status'=>$this->status,
          'sequence'=>$this->sequence,
          'image'=>$this->image?url($this->image):null,
        ];
    }
}
