<?php

namespace App\Http\Controllers\Api\Question;

use App\Actions\Question\DestroyQuestionAction;
use App\Actions\Question\StoreQuestionAction;
use App\Actions\Question\UpdateQuestionAction;
use App\DataTransferObjects\Question\CreateQuestionDTO;
use App\DataTransferObjects\Question\UpdateQuestionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Question\StoreQuestionRequest;
use App\Http\Requests\Api\Question\UpdateQuestionRequest;
use App\Http\Resources\Api\Question\QuestionResource;
use App\Models\Question;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function Laravel\Prompts\info;

/** 
 * @group Question Module
 * @authenticated
 */
class QuestionController extends Controller
{
    /**
     * List
     *
     * @queryParam type string Filter by question type. Example: single_choice
     * @queryParam published boolean Filter by published status. Example: true
     * @queryParam sort_by string The column to order by. Example: id
     * @queryParam sort_dir string Order direction (ASC|DESC). Example: DESC
     * @queryParam page integer Number of page. Example: 1.
     * @queryParam per_page integer Number of items per page. Example: 10.
     * @queryParam with_options boolean Include options in response. Example: true
     * @header X-Request-Language string Optional language for translated fields. Example: en
     *
     * @response 200 {
     *   "status": true,
     *   "message": "questions_fetched_successfully",
     *     "data": [
     *              {
     *                  "id": 1,
     *                  "type": "single_choice",
     *                  "question": "What is the capital of France?",
     *                  "mark": 5,
     *                  "published": true,
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     *             ],
     *   "meta": {
     *     "total": 40,
     *     "last_page": 4,
     *     "per_page": 10,
     *     "current_page": 1
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);

        $questions = Question::query()
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->when($request->filled('published'), fn($q) => $q->where('published', $request->boolean('published')))
            ->withCount('options')
            ->with('options')
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_dir', 'DESC'))
            ->paginate($perPage);

        return success_response(QuestionResource::collection($questions), true, 'questions_fetched_successfully');
    }

    /**
     * Store
     *
     * @param StoreQuestionRequest $request
     * @return JsonResponse
     *
     * @header X-Request-Language string Optional language for translated fields. Example: en
     * @response 201 {
     *  "status": true,
     *  "message": "success_question_created",
     *  "data": {
     *      "id": 1,
     *      "type": "single_choice",
     *      "question": "What is the capital of France?",
     *      "mark": 5,
     *      "published": true,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function store(StoreQuestionRequest $request, StoreQuestionAction $action): JsonResponse
    {
        try {
            $dto = new CreateQuestionDTO(
                type: $request->input('type'),
                question: $request->input('question'),
                mark: $request->integer('mark'),
                published: $request->boolean('published', false),
                options: $request->input('options', []),
            );
            info('Created CreateQuestionDTO', ['dto' => $dto]);

            $question = $action->execute($dto);

            return success_response(
                new QuestionResource($question),
                false,
                'success_question_created',
                201
            );
        } catch (Exception $e) {
            return error_response('question_creation_failed', 500);
        }
    }

    /**
     * Show
     *
     * @param Question $question
     *
     * @queryParam with_options boolean Include options in response. Example: true
     * @header X-Request-Language string Optional language for translated fields. Example: en
     * @response 200 {
     *   "status": true,
     *   "message": "question_fetched_successfully",
     *     "data": {
     *                  "id": 1,
     *                  "type": "single_choice",
     *                  "question": "What is the capital of France?",
     *                  "mark": 5,
     *                  "published": true,
     *                  "created_at": "2025-08-28T05:09:49.000000Z",
     *                  "updated_at": "2025-08-28T05:09:49.000000Z"
     *              }
     * }
     */
    public function show(Request $request, Question $question): JsonResponse
    {
        $question->load('options');

        return success_response(
            new QuestionResource($question),
            false,
            'question_fetched_successfully'
        );
    }

    /**
     * Update
     *
     * @param UpdateQuestionRequest $request
     * @param Question $question
     * @return JsonResponse
     *
     * @header X-Request-Language string Optional language for translated fields. Example: en
     * @response 200 {
     *  "status": true,
     *  "message": "success_question_updated",
     *  "data": {
     *      "id": 1,
     *      "type": "multiple_choice",
     *      "question": "What is the capital of England?",
     *      "mark": 10,
     *      "published": false,
     *      "created_at": "2025-08-28T08:49:59.000000Z",
     *      "updated_at": "2025-08-28T08:49:59.000000Z"
     *  }
     * }
     */
    public function update(UpdateQuestionRequest $request, Question $question, UpdateQuestionAction $action): JsonResponse
    {
        try {
            $dto = new UpdateQuestionDTO(
                type: $request->input('type'),
                question: $request->input('question'),
                mark: $request->filled('mark') ? $request->integer('mark') : null,
                published: $request->filled('published') ? $request->boolean('published') : null,
                options: $request->input('options'),
            );

            $updatedQuestion = $action->execute($question, $dto);

            return success_response(
                new QuestionResource($updatedQuestion),
                false,
                'success_question_updated'
            );
        } catch (Exception $e) {
            return error_response('question_update_failed', 500);
        }
    }

    /**
     * Delete
     *
     * Delete the specified question and all its options (cascade).
     *
     * @param Question $question
     * @header X-Request-Language string Optional language for translated fields. Example: en
     *
     * @response 200 {
     *   "status": true,
     *   "message": "success_question_deleted",
     *   "data": []
     * }
     */
    public function destroy(Question $question, DestroyQuestionAction $action): JsonResponse
    {
        $action->execute($question);

        return success_response([], false, 'success_question_deleted');
    }
}
