<?php

namespace App\Actions\Question;

use App\DataTransferObjects\Question\UpdateQuestionDTO;
use App\Enums\AppLanguageEnum;
use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\info;

class UpdateQuestionAction
{
    /**
     * @throws Exception
     */
    public function execute(Question $question, UpdateQuestionDTO $dto): Question
    {
        info('UpdateQuestionAction called');
        DB::beginTransaction();

        try {
            if ($dto->type !== null) {
                $question->type = $dto->type;
            }

            if ($dto->mark !== null) {
                $question->mark = $dto->mark;
            }

            if ($dto->published !== null) {
                $question->published = $dto->published;
            }

            $question->save();

            foreach ($question->translatable as $field) {
                if (property_exists($dto, $field) && $dto->{$field} !== null) {
                    $value = $dto->{$field};
                    foreach (AppLanguageEnum::cases() as $langEnum) {
                        $question->setTranslation($field, $value, $langEnum->value);
                    }
                }
            }

            // Update options if provided (delete all old and create new ones)
            if ($dto->options !== null) {
                // Delete existing options
                $question->options()->delete();

                // Create new options
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
