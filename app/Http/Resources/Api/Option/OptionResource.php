<?php

namespace App\Http\Resources\Api\Option;

use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
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
            'question_id'   => $this->question_id,
            'option_text'   => $this->getTranslation('option_text', $lang),
            'is_correct'    => $this->is_correct,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
