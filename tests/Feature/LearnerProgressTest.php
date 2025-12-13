<?php

namespace Tests\Feature;

use App\Models\Learner;
use App\Models\Course;
use App\Models\Enrolment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed test data
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create learners
        $learner1 = Learner::create(['firstname' => 'John', 'lastname' => 'Doe']);
        $learner2 = Learner::create(['firstname' => 'Jane', 'lastname' => 'Smith']);
        $learner3 = Learner::create(['firstname' => 'Bob', 'lastname' => 'Johnson']);

        // Create courses
        $math = Course::create(['name' => 'Mathematics']);
        $science = Course::create(['name' => 'Science']);
        $english = Course::create(['name' => 'English']);

        // Create enrolments with varying progress
        Enrolment::create(['learner_id' => $learner1->id, 'course_id' => $math->id, 'progress' => 85.50]);
        Enrolment::create(['learner_id' => $learner1->id, 'course_id' => $science->id, 'progress' => 92.00]);

        Enrolment::create(['learner_id' => $learner2->id, 'course_id' => $math->id, 'progress' => 45.00]);
        Enrolment::create(['learner_id' => $learner2->id, 'course_id' => $english->id, 'progress' => 78.25]);

        Enrolment::create(['learner_id' => $learner3->id, 'course_id' => $science->id, 'progress' => 0.00]);
    }

    public function test_learner_progress_page_loads_successfully(): void
    {
        $response = $this->get('/learner-progress');

        $response->assertStatus(200);
        $response->assertViewIs('learner-progress.index');
        $response->assertViewHas(['learners', 'courses', 'selectedCourse', 'sortDirection']);
    }

    public function test_page_displays_all_learners_by_default(): void
    {
        $response = $this->get('/learner-progress');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
        $response->assertSee('Bob Johnson');
    }

    public function test_page_displays_learner_enrolments_with_progress(): void
    {
        $response = $this->get('/learner-progress');

        $response->assertStatus(200);
        // Check for course names
        $response->assertSee('Mathematics');
        $response->assertSee('Science');
        $response->assertSee('English');
        // Check for progress values
        $response->assertSee('85.50%');
        $response->assertSee('92.00%');
        $response->assertSee('45.00%');
        $response->assertSee('78.25%');
        $response->assertSee('0.00%');
    }

    public function test_can_filter_learners_by_course(): void
    {
        $math = Course::where('name', 'Mathematics')->first();

        $response = $this->get("/learner-progress?course_id={$math->id}");

        $response->assertStatus(200);
        // Should see learners enrolled in Math
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');
        // Should not see learner only in Science
        $response->assertDontSee('Bob Johnson');
    }

    public function test_course_filter_shows_only_matching_enrolments(): void
    {
        $science = Course::where('name', 'Science')->first();

        $response = $this->get("/learner-progress?course_id={$science->id}");

        $response->assertStatus(200);
        $response->assertSee('Science');

        // When filtering by Science, only learners enrolled in Science should appear
        $learners = $response->viewData('learners');

        // All displayed learners should have Science enrolments
        foreach ($learners as $learner) {
            $this->assertNotEmpty($learner['enrolments'], "Learner {$learner['full_name']} has no enrolments");
            $courseNames = $learner['enrolments']->pluck('course_name')->all();
            $this->assertContains('Science', $courseNames, "Learner {$learner['full_name']} should be enrolled in Science");
        }
    }

    public function test_can_sort_learners_by_progress_ascending(): void
    {
        $response = $this->get('/learner-progress?sort=asc');

        $response->assertStatus(200);

        $content = $response->getContent();
        // Bob (0% avg) should appear before Jane (61.625% avg) who should appear before John (88.75% avg)
        $bobPosition = strpos($content, 'Bob Johnson');
        $janePosition = strpos($content, 'Jane Smith');
        $johnPosition = strpos($content, 'John Doe');

        $this->assertLessThan($janePosition, $bobPosition, 'Bob should appear before Jane in ascending sort');
        $this->assertLessThan($johnPosition, $janePosition, 'Jane should appear before John in ascending sort');
    }

    public function test_can_sort_learners_by_progress_descending(): void
    {
        $response = $this->get('/learner-progress?sort=desc');

        $response->assertStatus(200);

        $content = $response->getContent();
        // John (88.75% avg) should appear before Jane (61.625% avg) who should appear before Bob (0% avg)
        $johnPosition = strpos($content, 'John Doe');
        $janePosition = strpos($content, 'Jane Smith');
        $bobPosition = strpos($content, 'Bob Johnson');

        $this->assertLessThan($janePosition, $johnPosition, 'John should appear before Jane in descending sort');
        $this->assertLessThan($bobPosition, $janePosition, 'Jane should appear before Bob in descending sort');
    }

    public function test_can_combine_course_filter_and_sorting(): void
    {
        $math = Course::where('name', 'Mathematics')->first();

        $response = $this->get("/learner-progress?course_id={$math->id}&sort=desc");

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Jane Smith');

        $content = $response->getContent();
        $johnPosition = strpos($content, 'John Doe');
        $janePosition = strpos($content, 'Jane Smith');

        // John (85.5% in Math) should appear before Jane (45% in Math)
        $this->assertLessThan($janePosition, $johnPosition, 'John should appear before Jane when filtering Math and sorting descending');
    }

    public function test_handles_invalid_course_id_gracefully(): void
    {
        $response = $this->get('/learner-progress?course_id=99999');

        $response->assertStatus(302); // Validation fails, redirects back
    }

    public function test_handles_invalid_sort_parameter_gracefully(): void
    {
        $response = $this->get('/learner-progress?sort=invalid');

        $response->assertStatus(302); // Validation fails, redirects back
    }

    public function test_learner_with_no_enrolments_displays_appropriately(): void
    {
        // Create learner with no enrolments
        $newLearner = Learner::create(['firstname' => 'New', 'lastname' => 'Learner']);

        $response = $this->get('/learner-progress');

        $response->assertStatus(200);
        $response->assertSee('New Learner');
        // Should show zero progress
        $response->assertSee('0.00%');
    }

    public function test_enrolment_with_null_progress_displays_as_zero(): void
    {
        $learner = Learner::create(['firstname' => 'Test', 'lastname' => 'User']);
        $course = Course::create(['name' => 'Test Course']);
        Enrolment::create(['learner_id' => $learner->id, 'course_id' => $course->id, 'progress' => null]);

        $response = $this->get('/learner-progress');

        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('Test Course');
        $response->assertSee('0.00%');
    }

    public function test_average_progress_calculated_correctly(): void
    {
        $response = $this->get('/learner-progress');
        $learners = $response->viewData('learners');

        // John: (85.5 + 92) / 2 = 88.75
        $john = $learners->firstWhere('full_name', 'John Doe');
        $this->assertEquals(88.75, $john['average_progress']);

        // Jane: (45 + 78.25) / 2 = 61.625, rounded to 61.63
        $jane = $learners->firstWhere('full_name', 'Jane Smith');
        $this->assertEquals(61.63, $jane['average_progress']);

        // Bob: 0 / 1 = 0
        $bob = $learners->firstWhere('full_name', 'Bob Johnson');
        $this->assertEquals(0.00, $bob['average_progress']);
    }

    public function test_courses_dropdown_contains_all_courses(): void
    {
        $response = $this->get('/learner-progress');
        $courses = $response->viewData('courses');

        $this->assertCount(3, $courses);
        $this->assertTrue($courses->pluck('name')->contains('Mathematics'));
        $this->assertTrue($courses->pluck('name')->contains('Science'));
        $this->assertTrue($courses->pluck('name')->contains('English'));
    }

    public function test_selected_course_is_passed_to_view(): void
    {
        $math = Course::where('name', 'Mathematics')->first();

        $response = $this->get("/learner-progress?course_id={$math->id}");
        $selectedCourse = $response->viewData('selectedCourse');

        $this->assertEquals($math->id, $selectedCourse);
    }

    public function test_no_n_plus_one_queries(): void
    {
        // Enable query logging
        \DB::enableQueryLog();

        $this->get('/learner-progress');

        $queries = \DB::getQueryLog();

        // Should have minimal queries due to eager loading:
        // 1. Load learners with enrolments and courses (1-2 queries with joins/eager loading)
        // 2. Load all courses for dropdown (1 query)
        // Expect no more than 4 queries total
        $this->assertLessThanOrEqual(4, count($queries), 'Should use eager loading to prevent N+1 queries');
    }
}
