<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'title'=>$this->title,
            'isbn'=>$this->isbn,
            'edition'=>$this->edition,
            'number_of_page'=>$this->number_of_page,
            'video_url'=>$this->video_url,
            'publisher_id'=>$this->publisher_id,
            'language_id'=>$this->language_id,
            'country_id'=>$this->country_id,
            'category_id'=>$this->category_id,
            'subcategory_id'=>$this->subcategory_id,
            'third_category_id'=>$this->third_category_id,
            'summary'=>$this->summary,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
            'brochure'=>$this->brochure?url($this->brochure):'',
            'itemAuthors'=>ItemAuthorResourceCollection::make($this->whenLoaded('itemAuthors')),
            'itemThumbnails'=>ItemThumbnailResourceCollection::make($this->whenLoaded('itemThumbnails')),
        ];
    }
}
