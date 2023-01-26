<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PublisherResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'publishers'=>$this->collection,
//            'pagination'=>[
//                'first_page_url' => $this->path().'?page=1',
//                'last_page' => $this->lastPage(),
//                'last_page_url' => $this->path().'?page='.$this->lastPage(),
//                'next_page_url' => $this->nextPageUrl(),
//                'path' => $this->path(),
//                'per_page' => $this->perPage(),
//                'prev_page_url' => $this->previousPageUrl(),
//                'total' => $this->total(),
//                'count' => $this->count(),
//                'current_page' => $this->currentPage(),
//            ]
        ];
    }
}
