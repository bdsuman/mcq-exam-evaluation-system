<?php

namespace Database\Seeders;

use App\Enums\AppLanguageEnum;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::factory()->count(20)->create();

        foreach ($tasks as $item) {
            foreach (['title', 'description'] as $field) {
                foreach (AppLanguageEnum::cases() as $lang) {
                    $locale = $lang->value;
                    $item->setTranslation($field, generateTextByLength(20)." ($locale)", $locale);
                }
            }
        }
    }
}
