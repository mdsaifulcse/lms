<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMembershipPlanResource extends JsonResource
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
            'user_id'=>$this->user_id,
            'membership_plan_id'=>$this->membership_plan_id,
            'user_name'=>$this->user->name,
            'membership_plan'=>$this->membershipPlan->name,
            'valid_till'=>$this->valid_till,
            'status'=>$this->status,
        ];
    }
}
