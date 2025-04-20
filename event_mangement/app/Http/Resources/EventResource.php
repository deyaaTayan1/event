<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id ,
            'name' => $this->name ,
            'description' => $this->description ,
            'start_time' => $this->start_time ,
            'end_time' => $this->end_time ,
            'user' => new UserResource($this->whenLoaded('user')) ,
            'attendees' => AttendeeResource::collection( $this->whenLoaded('attendees') ) ,
        ];
    }
}
