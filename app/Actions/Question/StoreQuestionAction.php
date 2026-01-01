<?php

namespace App\Actions\Question;

use App\DataTransferObjects\Question\CreateQuestionDTO;
use App\Enums\AppLanguageEnum;
use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\info;

class StoreQuestionAction
{
    /**
     * @throws Exception
     */
    public function execute(CreateQuestionDTO $dto): Question
    {
        info('StoreQuestionAction called');
        DB::beginTransaction();

        try {
            $question = new Question();
            $question->type = $dto->type;
            $question->question = $dto->question;
            $question->mark = $dto->mark;
            $question->published = $dto->published;

            $question->save();

            foreach ($question->translatable as $field) {
                if (property_exists($dto, $field)) {
                    $value = $dto->{$field};
                    foreach (AppLanguageEnum::cases() as $langEnum) {
                        $question->setTranslation($field, $value, $langEnum->value);
                    }
                }
            }

            // Create options if provided
            if (!empty($dto->options)) {
                foreach ($dto->options as $optionData) {
                    $option = $question->options()->create([
                        'option_text' => $optionData['option_text'],
                        'is_correct' => $optionData['is_correct'],
                    ]);

                    // Set translations for option_text
                    foreach (AppLanguageEnum::cases() as $langEnum) {
                        $option->setTranslation('option_text', $optionData['option_text'], $langEnum->value);
                    }
                }
            }

            DB::commit();

            // Load options for response
            $question->load('options');

            return $question;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
