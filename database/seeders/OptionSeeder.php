<?php

namespace Database\Seeders;

use App\Enums\AppLanguageEnum;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First ensure we have questions to attach options to
        $questions = Question::all();

        if ($questions->isEmpty()) {
            $this->command->warn('No questions found. Please run QuestionSeeder first.');
            return;
        }

        // Create multiple options for each question
        foreach ($questions as $question) {
            // Create 4 options per question (typical MCQ format)
            $optionsCount = rand(3, 5);
            
            for ($i = 0; $i < $optionsCount; $i++) {
                $option = Option::factory()->create([
                    'question_id' => $question->id,
                    'is_correct' => $i === 0, // Make first option correct by default
                ]);

                // Add translations for option_text
                foreach ($option->translatable as $field) {
                    foreach (AppLanguageEnum::cases() as $lang) {
                        $locale = $lang->value;
                        $option->setTranslation($field, generateTextByLength(30) . " ($locale)", $locale);
                    }
                }
            }
        }

        $this->command->info('Options seeded successfully with translations!');
    }
}
