<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\SubmitAnswersRequest;
use App\Http\Resources\Api\Student\Question\QuestionResource;
use App\Models\Question;
use App\Models\QuestionSubmission;
use App\Models\QuestionSubmissionAnswer;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    /**
     * List published questions with options (no correctness flags exposed).
     */
    public function index(): JsonResponse
    {
        $userId = request()->user()->id;

        $submittedQuestionIds = QuestionSubmissionAnswer::query()
            ->where('user_id', $userId)
            ->pluck('question_id')
            ->all();

        $questions = Question::query()
            ->where('published', true)
            ->with(['options:id,question_id,option_text,is_correct'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($question) use ($submittedQuestionIds) {
                $question->is_submitted = in_array($question->id, $submittedQuestionIds, true);
                return $question;
            });

        return success_response(QuestionResource::collection($questions));
    }

    /**
     * Submit answers and get score summary.
     */
    public function submit(SubmitAnswersRequest $request): JsonResponse
    {
        $responses = collect($request->validated('responses'));
        $userId = $request->user()->id;

        $questionIds = $responses->pluck('question_id');

        $alreadySubmitted = QuestionSubmissionAnswer::query()
            ->where('user_id', $userId)
            ->whereIn('question_id', $questionIds)
            ->pluck('question_id')
            ->all();

        if (!empty($alreadySubmitted)) {
            return error_response(__('already_submitted'), 422);
        }

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
                'selected_option_ids' => $selectedOptionIds->values()->all(),
                'correct_option_ids' => $correctOptionIds->values()->all(),
                'is_correct' => $isCorrect,
            ];
        }

        // Persist submission summary
        QuestionSubmission::create([
            'user_id' => $userId,
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'questions_answered' => $responses->count(),
            'correct_answers' => $correctCount,
        ]);

        // Persist per-question answers for review
        $now = now();

        QuestionSubmissionAnswer::insert(collect($details)->map(function (array $detail) use ($userId, $now) {
            return [
                'user_id' => $userId,
                'question_id' => $detail['question_id'],
                'selected_option_ids' => json_encode($detail['selected_option_ids']),
                'correct_option_ids' => json_encode($detail['correct_option_ids']),
                'mark' => $detail['mark'],
                'obtained_marks' => $detail['is_correct'] ? $detail['mark'] : 0,
                'is_correct' => $detail['is_correct'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all());

        return success_response([
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'questions_answered' => $responses->count(),
            'correct_answers' => $correctCount,
            'details' => $details,
        ]);
    }

    /**
     * Show a student's existing submission for a question.
     */
    public function submission(Question $question): JsonResponse
    {
        $submission = QuestionSubmissionAnswer::query()
            ->where('user_id', request()->user()->id)
            ->where('question_id', $question->id)
            ->first();

        if (!$submission) {
            return success_response([
                'question_id' => $question->id,
                'is_submitted' => false,
                'selected_option_ids' => [],
                'correct_option_ids' => [],
                'is_correct' => false,
                'obtained_marks' => 0,
                'total_marks' => (float) $question->mark,
                'submitted_at' => null,
            ]);
        }

        return success_response([
            'question_id' => $question->id,
            'is_submitted' => true,
            'selected_option_ids' => $submission->selected_option_ids ?? [],
            'correct_option_ids' => $submission->correct_option_ids ?? [],
            'is_correct' => (bool) $submission->is_correct,
            'obtained_marks' => (float) $submission->obtained_marks,
            'total_marks' => (float) $submission->mark,
            'submitted_at' => $submission->created_at,
        ]);
    }
}
