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
            'itemQty'=>$this->whenLoaded('itemInventory'),
            'title'=>$this->title,
            'isbn'=>$this->isbn,
            'edition'=>$this->edition,
            'number_of_page'=>$this->number_of_page,
            'video_url'=>$this->video_url,
            'publisher_id'=>$this->publisher_id,
            'publisher'=>$this->publisher?$this->publisher->name:'',
            'language_id'=>$this->language_id,
            'language'=>$this->language?$this->language->name:'',
            'country_id'=>$this->country_id,
            'country'=>$this->country?$this->country->name:'',
            'category_id'=>$this->category_id,
            'category'=>$this->category?$this->category->name:'',
            'sub_category_id'=>$this->sub_category_id,
            'sub_category'=>$this->subCategory?$this->subCategory->name:'',
            'third_category_id'=>$this->third_category_id,
            'third_category'=>$this->thirdSubCategory?$this->thirdSubCategory->name:'',
            'summary'=>$this->summary,
            'publish_status'=>$this->publish_status,
            'show_home'=>$this->show_home,
            'status'=>$this->status,
            'sequence'=>$this->sequence,
            'brochure'=>$this->brochure?url($this->brochure):'',
            //'itemAuthors'=>ItemAuthorResourceCollection::make($this->whenLoaded('itemAuthors')),
            'relItemAuthorsName'=>$this->whenLoaded('relItemAuthorsName'), // $this->relItemAuthorsName,
            'itemThumbnails'=>ItemThumbnailResourceCollection::make($this->whenLoaded('itemThumbnails')),
        ];
    }
}
