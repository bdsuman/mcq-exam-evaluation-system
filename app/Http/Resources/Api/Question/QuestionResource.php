<?php

namespace App\Http\Resources\Api\Question;

use App\Http\Resources\Api\Option\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'type'          => $this->type,
            'question'      => $this->getTranslation('question', $lang),
            'mark'          => $this->mark,
            'published'     => $this->published,
            'options'       => OptionResource::collection($this->whenLoaded('options')),
            'options_count' => $this->whenCounted('options'),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
