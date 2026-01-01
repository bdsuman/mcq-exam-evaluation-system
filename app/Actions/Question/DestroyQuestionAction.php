<?php

namespace App\Actions\Question;

use App\Models\Question;
use Exception;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\info;

class DestroyQuestionAction
{
    /**
     * @throws Exception
     */
    public function execute(Question $question): bool
    {
        info('DestroyQuestionAction called');
        DB::beginTransaction();

        try {
            // Delete all related options (will cascade)
            $question->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
