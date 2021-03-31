<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Blog extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
        // return [
        //   id: $this->id,
        //   'isPublished': $this->isPublished,  
        //   'title': $this->title,
        //   'content' = $this->post_content,
        //   'url' = $this->email,
        //   'image' = $this->author
        // ];
    }
}
