<?php

namespace App\Http\Resources\Api\Task;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        $lang = $request->header('X-Request-Language') ?? app('language');

        return [
            'id'            => $this->id,
            'title'         => $this->getTranslation('title', $lang),
            'description'   => $this->getTranslation('description', $lang),
            'avatar'        => $this->avatar,
            'avatar_url'    => $this->avatar_url,
            'is_completed'  => $this->is_completed,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
