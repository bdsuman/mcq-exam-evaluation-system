<?php

namespace Database\Seeders;

use App\Enums\AppLanguageEnum;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = Question::factory()->count(20)->create();

        foreach ($questions as $item) {
            foreach ($item->translatable as $field) {
                foreach (AppLanguageEnum::cases() as $lang) {
                    $locale = $lang->value;
                    $item->setTranslation($field, generateTextByLength(50) . " ($locale)", $locale);
                }
            }
        }

        $this->command->info('Questions seeded successfully with translations!');
    }
}
