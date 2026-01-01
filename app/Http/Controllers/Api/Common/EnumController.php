<?php

namespace App\Http\Controllers\Api\Common;

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
}
