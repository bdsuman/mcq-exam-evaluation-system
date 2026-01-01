<?php

namespace App\Http\Controllers\Api\Common;

use App\Enums\QuestionType;
use App\Enums\UserGenderEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
/**
 * Enum Controller
 * 
 * @group Common
 */
class EnumController extends Controller
{
    /**
     * Get Gender Enum options
     * 
     * @return JsonResponse
     */
    public function genderOptions(): JsonResponse
    {
        $options = array_map(function (UserGenderEnum $case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }, UserGenderEnum::cases());

        return success_response($options);
    }

    /**
     * Get Question Type Enum options
     * 
     * @return JsonResponse
     */
    public function questionTypeOptions(): JsonResponse
    {
        $options = array_map(function (QuestionType $case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }, QuestionType::cases());

        return success_response($options);
    }
}
