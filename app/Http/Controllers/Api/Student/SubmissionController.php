<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\QuestionSubmission;
use Illuminate\Http\JsonResponse;

class SubmissionController extends Controller
{
    public function stats(): JsonResponse
    {
        $user = request()->user();

        $totals = QuestionSubmission::query()
            ->where('user_id', $user->id)
            ->selectRaw('COUNT(*) as attempts, COALESCE(SUM(obtained_marks),0) as obtained_marks, COALESCE(SUM(total_marks),0) as total_marks')
            ->first();

        return success_response([
            'attempts' => (int) ($totals->attempts ?? 0),
            'obtained_marks' => (float) ($totals->obtained_marks ?? 0),
            'total_marks' => (float) ($totals->total_marks ?? 0),
        ]);
    }
}
