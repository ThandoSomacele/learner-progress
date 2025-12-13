<?php

namespace App\Http\Controllers;

use App\Services\LearnerProgressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearnerProgressController extends Controller
{
    /**
     * The learner progress service instance.
     */
    protected LearnerProgressService $progressService;

    /**
     * Create a new controller instance.
     */
    public function __construct(LearnerProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Display the learner progress dashboard.
     */
    public function index(Request $request): View
    {
        // Validate optional query parameters
        $validated = $request->validate([
            'course_id' => 'nullable|integer|exists:courses,id',
            'sort' => 'nullable|in:asc,desc',
        ]);

        $courseId = $validated['course_id'] ?? null;
        $sortDirection = $validated['sort'] ?? null;

        // Get learners with progress data
        $learners = $this->progressService->getLearnersWithProgress($courseId);

        // Apply sorting if requested
        if ($sortDirection) {
            $learners = $this->progressService->sortByProgress($learners, $sortDirection);
        }

        // Get all courses for filter dropdown
        $courses = $this->progressService->getAllCourses();

        return view('learner-progress.index', [
            'learners' => $learners,
            'courses' => $courses,
            'selectedCourse' => $courseId,
            'sortDirection' => $sortDirection,
        ]);
    }
}
