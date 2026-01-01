<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\SubmitAnswersRequest;
use App\Http\Resources\Api\Student\Question\QuestionResource;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * List published questions with options (no correctness flags exposed).
     */
    public function index(): JsonResponse
    {
        $questions = Question::query()
            ->where('published', true)
            ->with(['options:id,question_id,option_text,is_correct'])
            ->orderByDesc('id')
            ->get();

        return success_response(QuestionResource::collection($questions));
    }

    /**
     * Submit answers and get score summary.
     */
    public function submit(SubmitAnswersRequest $request): JsonResponse
    {
        $responses = collect($request->validated('responses'));

        // Fetch all relevant questions with options
        $questions = Question::query()
            ->whereIn('id', $responses->pluck('question_id'))
            ->where('published', true)
            ->with('options:id,question_id,is_correct')
            ->get()
            ->keyBy('id');

        $totalMarks = 0;
        $obtainedMarks = 0;
        $correctCount = 0;

        $details = [];

        foreach ($responses as $response) {
            $questionId = $response['question_id'];
            $selectedOptionIds = collect($response['option_ids'])->unique()->values();
            $question = $questions->get($questionId);

            if (!$question) {
                // Question not published or not found; skip scoring
                continue;
            }

            // Ensure only options that belong to the question are considered
            $validOptionIds = $question->options->pluck('id');
            $selectedOptionIds = $selectedOptionIds->filter(fn ($id) => $validOptionIds->contains($id))->values();

            $totalMarks += (float) $question->mark;

            $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->values();

            // Award marks only if:
            // - The student selected at least one option
            // - All selected options are correct
            // - All correct options were selected (exact match for multi-correct)
            $isCorrect = $correctOptionIds->isNotEmpty()
                && $selectedOptionIds->isNotEmpty()
                && $selectedOptionIds->diff($correctOptionIds)->isEmpty()
                && $correctOptionIds->diff($selectedOptionIds)->isEmpty();

            if ($isCorrect) {
                $obtainedMarks += (float) $question->mark;
                $correctCount++;
            }

            $details[] = [
                'question_id' => $questionId,
                'mark' => (float) $question->mark,
                'selected_option_ids' => $selectedOptionIds,
                'correct_option_ids' => $correctOptionIds,
                'is_correct' => $isCorrect,
            ];
        }

        return success_response([
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'questions_answered' => $responses->count(),
            'correct_answers' => $correctCount,
            'details' => $details,
        ]);
    }
}
