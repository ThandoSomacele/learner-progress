<?php

namespace Tests\Unit;

use App\Models\Learner;
use App\Models\Course;
use App\Models\Enrolment;
use App\Services\LearnerProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LearnerProgressService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LearnerProgressService();
    }

    public function test_get_all_courses_returns_sorted_courses(): void
    {
        Course::create(['name' => 'Zoology']);
        Course::create(['name' => 'Biology']);
        Course::create(['name' => 'Chemistry']);

        $courses = $this->service->getAllCourses();

        $this->assertCount(3, $courses);
        $this->assertEquals('Biology', $courses[0]->name);
        $this->assertEquals('Chemistry', $courses[1]->name);
        $this->assertEquals('Zoology', $courses[2]->name);
    }

    public function test_get_learners_with_progress_returns_structured_data(): void
    {
        $learner = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);
        $course = Course::create(['name' => 'Math']);
        Enrolment::create(['learner_id' => $learner->id, 'course_id' => $course->id, 'progress' => 75.50]);

        $result = $this->service->getLearnersWithProgress();

        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['full_name']);
        $this->assertArrayHasKey('enrolments', $result[0]);
        $this->assertArrayHasKey('average_progress', $result[0]);
    }

    public function test_filters_learners_by_course(): void
    {
        $learner1 = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);
        $learner2 = Learner::create(['firstname' => 'Jane', 'lastname' => 'Smith']);

        $math = Course::create(['name' => 'Math']);
        $science = Course::create(['name' => 'Science']);

        Enrolment::create(['learner_id' => $learner1->id, 'course_id' => $math->id, 'progress' => 80.00]);
        Enrolment::create(['learner_id' => $learner2->id, 'course_id' => $science->id, 'progress' => 90.00]);

        $result = $this->service->getLearnersWithProgress($math->id);

        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['full_name']);
    }

    public function test_calculates_average_progress_correctly(): void
    {
        $learner = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);
        $course1 = Course::create(['name' => 'Math']);
        $course2 = Course::create(['name' => 'Science']);

        Enrolment::create(['learner_id' => $learner->id, 'course_id' => $course1->id, 'progress' => 80.00]);
        Enrolment::create(['learner_id' => $learner->id, 'course_id' => $course2->id, 'progress' => 90.00]);

        $result = $this->service->getLearnersWithProgress();

        // Average: (80 + 90) / 2 = 85.00
        $this->assertEquals(85.00, $result[0]['average_progress']);
    }

    public function test_handles_null_progress_values(): void
    {
        $learner = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);
        $course = Course::create(['name' => 'Math']);
        Enrolment::create(['learner_id' => $learner->id, 'course_id' => $course->id, 'progress' => null]);

        $result = $this->service->getLearnersWithProgress();

        $this->assertEquals('0.00%', $result[0]['enrolments'][0]['progress']);
        $this->assertEquals(0.00, $result[0]['average_progress']);
    }

    public function test_sort_by_progress_ascending(): void
    {
        $learners = collect([
            ['id' => 1, 'full_name' => 'High Achiever', 'average_progress' => 90.0, 'enrolments' => collect()],
            ['id' => 2, 'full_name' => 'Low Achiever', 'average_progress' => 30.0, 'enrolments' => collect()],
            ['id' => 3, 'full_name' => 'Mid Achiever', 'average_progress' => 60.0, 'enrolments' => collect()],
        ]);

        $sorted = $this->service->sortByProgress($learners, 'asc');

        $this->assertEquals('Low Achiever', $sorted[0]['full_name']);
        $this->assertEquals('Mid Achiever', $sorted[1]['full_name']);
        $this->assertEquals('High Achiever', $sorted[2]['full_name']);
    }

    public function test_sort_by_progress_descending(): void
    {
        $learners = collect([
            ['id' => 1, 'full_name' => 'High Achiever', 'average_progress' => 90.0, 'enrolments' => collect()],
            ['id' => 2, 'full_name' => 'Low Achiever', 'average_progress' => 30.0, 'enrolments' => collect()],
            ['id' => 3, 'full_name' => 'Mid Achiever', 'average_progress' => 60.0, 'enrolments' => collect()],
        ]);

        $sorted = $this->service->sortByProgress($learners, 'desc');

        $this->assertEquals('High Achiever', $sorted[0]['full_name']);
        $this->assertEquals('Mid Achiever', $sorted[1]['full_name']);
        $this->assertEquals('Low Achiever', $sorted[2]['full_name']);
    }

    public function test_formats_progress_with_two_decimal_places(): void
    {
        $learner = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);
        $course = Course::create(['name' => 'Math']);
        Enrolment::create(['learner_id' => $learner->id, 'course_id' => $course->id, 'progress' => 75.555]);

        $result = $this->service->getLearnersWithProgress();

        // Should be rounded to 2 decimal places
        $this->assertEquals('75.56%', $result[0]['enrolments'][0]['progress']);
    }

    public function test_handles_learner_with_no_enrolments(): void
    {
        $learner = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);

        $result = $this->service->getLearnersWithProgress();

        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['full_name']);
        $this->assertEmpty($result[0]['enrolments']);
        $this->assertEquals(0.00, $result[0]['average_progress']);
    }
}
