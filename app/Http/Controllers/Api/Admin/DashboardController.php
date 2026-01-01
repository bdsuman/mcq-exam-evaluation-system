<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionSubmissionAnswer;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $submissionsQuery = QuestionSubmissionAnswer::query();

        $totalSubmissions = $submissionsQuery->count();
        $correctSubmissions = (clone $submissionsQuery)
            ->where('is_correct', true)
            ->count();
        $incorrectSubmissions = $totalSubmissions - $correctSubmissions;
        $submittedStudents = (clone $submissionsQuery)
            ->distinct('user_id')
            ->count('user_id');
        $accuracyPercent = $totalSubmissions > 0
            ? round(($correctSubmissions / $totalSubmissions) * 100, 2)
            : 0;

        return success_response([
            'total_questions' => Question::count(),
            'total_users' => User::count(),
            'questions_answered' => $totalSubmissions,
            'submitted_students' => $submittedStudents,
            'correct_submissions' => $correctSubmissions,
            'incorrect_submissions' => $incorrectSubmissions,
            'accuracy_percent' => $accuracyPercent,
        ]);
    }
}
