<?php

namespace App\Services;

use App\Models\Learner;
use App\Models\Course;
use Illuminate\Support\Collection;

class LearnerProgressService
{
    /**
     * Get all learners with their enrolment data, optionally filtered by course.
     *
     * @param int|null $courseId Optional course ID to filter by
     * @return Collection
     */
    public function getLearnersWithProgress(?int $courseId = null): Collection
    {
        $query = Learner::query()
            ->with(['enrolments.course'])
            ->orderBy('firstname')
            ->orderBy('lastname');

        $learners = $query->get()->map(function ($learner) use ($courseId) {
            // Filter enrolments if course filter is applied
            $filteredEnrolments = $courseId
                ? $learner->enrolments->filter(fn($e) => $e->course_id === $courseId)
                : $learner->enrolments;

            return [
                'id' => $learner->id,
                'full_name' => $this->getFullName($learner),
                'enrolments' => $filteredEnrolments->map(function ($enrolment) {
                    return [
                        'course_name' => $enrolment->course->name,
                        'progress' => $this->formatProgress($enrolment->progress),
                    ];
                })->values(),
                'average_progress' => $this->calculateAverageProgress($filteredEnrolments),
            ];
        });

        // Remove learners with no enrolments when filtering by course
        if ($courseId) {
            $learners = $learners->filter(function ($learner) {
                return $learner['enrolments']->isNotEmpty();
            })->values();
        }

        return $learners;
    }

    /**
     * Get all courses for the filter dropdown.
     *
     * @return Collection
     */
    public function getAllCourses(): Collection
    {
        return Course::orderBy('name')->get();
    }

    /**
     * Sort learners by average progress.
     *
     * @param Collection $learners
     * @param string $direction 'asc' or 'desc'
     * @return Collection
     */
    public function sortByProgress(Collection $learners, string $direction = 'asc'): Collection
    {
        return $learners->sortBy('average_progress', SORT_REGULAR, $direction === 'desc')
                       ->values();
    }

    /**
     * Get formatted full name.
     *
     * @param Learner $learner
     * @return string
     */
    private function getFullName(Learner $learner): string
    {
        return trim($learner->firstname . ' ' . $learner->lastname);
    }

    /**
     * Format progress as percentage string.
     *
     * @param float|null $progress
     * @return string
     */
    private function formatProgress(?float $progress): string
    {
        return $progress !== null ? number_format($progress, 2) . '%' : '0.00%';
    }

    /**
     * Calculate average progress across enrolments.
     *
     * @param Collection $enrolments
     * @return float
     */
    private function calculateAverageProgress(Collection $enrolments): float
    {
        if ($enrolments->isEmpty()) {
            return 0.0;
        }

        $total = $enrolments->sum('progress');
        return round($total / $enrolments->count(), 2);
    }
}
