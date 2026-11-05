<?php

use App\Services\AdminDashboardSummaryService;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use Config\Database;
use Tests\Support\IsolatedFeatureSession;

/**
 * @internal
 */
final class AdminDashboardTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use IsolatedFeatureSession;
    use AuthenticationTesting;

    protected $namespace = null;

    protected function setUp(): void
    {
        $database = config(Database::class);
        $this->assertSame('SQLite3', $database->tests['DBDriver']);
        $this->assertSame(':memory:', $database->tests['database']);
        parent::setUp();
    }

    public function testAdminSeesOnlyAggregateStudentActivityCards(): void
    {
        $firstStudent = $this->createUser('admin-summary-student-a', ['student']);
        $secondStudent = $this->createUser('admin-summary-student-b', ['student']);
        $admin = $this->createUser('admin-summary-admin', ['admin']);
        $mixedRole = $this->createUser('admin-summary-mixed', ['student', 'admin']);

        $firstSubject = $this->createSubject((int) $firstStudent->id, 'Private first subject');
        $secondSubject = $this->createSubject((int) $firstStudent->id, 'Private second subject');
        $thirdSubject = $this->createSubject((int) $secondStudent->id, 'Private third subject');
        $this->createTopic($firstSubject, 'Private first topic');
        $this->createTopic($firstSubject, 'Private second topic');
        $this->createTopic($thirdSubject, 'Private third topic');
        $this->createSession((int) $firstStudent->id, $firstSubject, 25);
        $this->createSession((int) $secondStudent->id, $thirdSubject, 45);
        $this->createPlan((int) $firstStudent->id, date('Y-m-d'));
        $this->createPlan((int) $secondStudent->id, date('Y-m-d'));
        $this->createActivityDay((int) $firstStudent->id, date('Y-m-d'));
        $this->createActivityDay((int) $secondStudent->id, date('Y-m-d'));

        $adminSubject = $this->createSubject((int) $admin->id, 'Admin only subject');
        $this->createSession((int) $admin->id, $adminSubject, 90);
        $mixedSubject = $this->createSubject((int) $mixedRole->id, 'Mixed role subject');
        $this->createSession((int) $mixedRole->id, $mixedSubject, 30);

        $summary = (new AdminDashboardSummaryService())->summarize();
        $this->assertSame([
            'student_count' => 2,
            'subject_count' => 3,
            'topic_count' => 3,
            'saved_session_count' => 2,
            'total_study_minutes' => 70,
            'average_session_minutes' => 35.0,
            'saved_plan_count' => 2,
            'recorded_visits_30d' => null,
            'onboarding_completion_rate' => null,
        ], array_intersect_key($summary, array_flip([
            'student_count', 'subject_count', 'topic_count', 'saved_session_count', 'total_study_minutes',
            'average_session_minutes', 'saved_plan_count', 'recorded_visits_30d', 'onboarding_completion_rate',
        ])));
        $this->assertCount(8, $summary['weekly_visits']);
        $this->assertSame(array_fill(0, 8, null), array_column($summary['weekly_visits'], 'value'));
        $this->assertCount(6, $summary['monthly_registrations']);

        $this->authenticateAs($admin);
        $page = $this->getAuthenticated('admin');
        $page->assertOK();
        $page->assertSee('duno.');
        $page->assertDontSee('aria-label="Page navigation"');
        $page->assertDontSee('aria-label="Go back to the previous page"');
        $page->assertDontSee('aria-label="Go to home"');
        $page->assertSee('aria-label="Admin navigation"');
        $page->assertSee('aria-label="duno. home"');
        $page->assertSee('Student accounts');
        $page->assertSee('Subjects');
        $page->assertSee('Topics');
        $page->assertSee('Saved study sessions');
        $page->assertSee('Total study minutes');
        $page->assertSee('Saved daily plans');
        $page->assertSee('Average session length');
        $page->assertSee('Recorded visits');
        $page->assertSee('Program selection complete');
        $page->assertSee('Weekly visits and active students');
        $page->assertSee('New student accounts');
        $page->assertSee('70');
        $page->assertSee('system-wide aggregates only');
        $page->assertDontSee('admin-summary-student-a@example.test');
        $page->assertDontSee('Private first subject');
        $page->assertDontSee('Private first topic');

        $this->getAuthenticated('student')->assertStatus(403);
    }

    public function testAdminDashboardShowsZeroTotalsWhenThereIsNoStudentActivity(): void
    {
        $admin = $this->createUser('admin-summary-empty', ['admin']);
        $this->authenticateAs($admin);

        $page = $this->getAuthenticated('admin');
        $page->assertOK();
        $page->assertSee('Student accounts');
        $page->assertSee('Total study minutes');
        $page->assertSee('0');
        $this->assertSame([
            'student_count' => 0,
            'subject_count' => 0,
            'topic_count' => 0,
            'saved_session_count' => 0,
            'total_study_minutes' => 0,
            'average_session_minutes' => null,
            'saved_plan_count' => 0,
            'recorded_visits_30d' => null,
            'onboarding_completion_rate' => null,
        ], array_intersect_key((new AdminDashboardSummaryService())->summarize(), array_flip([
            'student_count', 'subject_count', 'topic_count', 'saved_session_count', 'total_study_minutes',
            'average_session_minutes', 'saved_plan_count', 'recorded_visits_30d', 'onboarding_completion_rate',
        ])));
    }

    public function testAdminChartsShowGroupsOnlyAfterPrivacyThresholdIsMet(): void
    {
        $students = [];
        for ($index = 1; $index <= 10; $index++) {
            $student = $this->createUser('admin-privacy-student-' . $index, ['student']);
            $students[] = $student;
            if ($index <= 5) {
                db_connect()->table('users')->where('id', (int) $student->id)->update([
                    'academic_program_selected_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $lastCompleteWeek = (new DateTimeImmutable('today', new DateTimeZone(config('App')->appTimezone)))
            ->modify('monday this week')->modify('-1 day')->format('Y-m-d');
        foreach (array_slice($students, 0, 5) as $student) {
            $this->createActivityDay((int) $student->id, $lastCompleteWeek);
        }

        $summary = (new AdminDashboardSummaryService())->summarize();
        $this->assertSame(10, $summary['student_count']);
        $this->assertSame(50.0, $summary['onboarding_completion_rate']);
        $this->assertSame(5, $summary['recorded_visits_30d']);
        $this->assertSame(5, $summary['weekly_visits'][7]['value']);
        $this->assertSame(10, $summary['monthly_registrations'][5]['value']);
    }

    public function testAdminPlanningPatternsSummarizePlansWhenPrivacyThresholdIsMet(): void
    {
        for ($index = 1; $index <= 10; $index++) {
            $student = $this->createUser('admin-planning-student-' . $index, ['student']);

            if ($index > 5) {
                continue;
            }

            $planId = $this->createPlan((int) $student->id, date('Y-m-d'));
            db_connect()->table('study_plan_items')->insert([
                'user_id' => (int) $student->id,
                'plan_id' => $planId,
                'topic_id' => null,
                'topic_title' => 'Synthetic review topic',
                'subject_name' => 'Synthetic subject',
                'duration_minutes' => 25,
                'position' => 1,
                'start_time' => null,
            ]);
        }

        $summary = (new AdminDashboardSummaryService())->summarize();

        $this->assertSame(5, $summary['planning_patterns']['planner_count']);
        $this->assertSame(50.0, $summary['planning_patterns']['coverage_percent']);
        $this->assertSame(1.0, $summary['planning_patterns']['median_plan_days']);
        $this->assertSame(25.0, $summary['planning_patterns']['median_planned_minutes']);
        $this->assertSame(1.0, $summary['planning_patterns']['median_blocks_per_day']);
        $this->assertSame(41.7, $summary['planning_patterns']['median_capacity_percent']);
    }

    public function testStudentAndSignedOutVisitorsCannotAccessAdminDashboard(): void
    {
        $student = $this->createUser('admin-summary-denied', ['student']);
        $this->authenticateAs($student);
        $denied = $this->getAuthenticated('admin');
        $denied->assertStatus(403);
        $response = $denied->response();
        $this->assertStringContainsString('no-store', $response->getHeaderLine('Cache-Control'));
        $this->assertSame('SAMEORIGIN', $response->getHeaderLine('X-Frame-Options'));
        $this->assertSame('nosniff', $response->getHeaderLine('X-Content-Type-Options'));
        $this->assertSame('same-origin', $response->getHeaderLine('Referrer-Policy'));

        auth()->logout();
        $response = $this->get('admin');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    /** @param list<string> $groups */
    private function createUser(string $username, array $groups): User
    {
        $model = new UserModel();
        $user = new User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'Synthetic-test-password-123!',
        ]);
        $userId = $model->insert($user);
        $created = $model->findById((int) $userId);
        $created->addGroup(...$groups);
        $created->activate();
        $created = $model->findById((int) $userId);

        return $created;
    }

    private function createSubject(int $ownerId, string $name): int
    {
        $db = db_connect();
        $db->table('subjects')->insert([
            'user_id' => $ownerId,
            'name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }

    private function createTopic(int $subjectId, string $title): int
    {
        $db = db_connect();
        $db->table('topics')->insert([
            'subject_id' => $subjectId,
            'title' => $title,
            'due_date' => null,
            'importance' => 'medium',
            'progress' => 'not_started',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }

    private function createSession(int $ownerId, int $subjectId, int $minutes): int
    {
        $db = db_connect();
        $db->table('study_sessions')->insert([
            'user_id' => $ownerId,
            'subject_id' => $subjectId,
            'topic_id' => null,
            'duration_minutes' => $minutes,
            'completed_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }

    private function createPlan(int $ownerId, string $date): int
    {
        $db = db_connect();
        $db->table('study_plans')->insert([
            'user_id' => $ownerId,
            'plan_date' => $date,
            'available_minutes' => 60,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }

    private function createActivityDay(int $ownerId, string $date): int
    {
        $db = db_connect();
        $db->table('student_streak_days')->insert([
            'user_id' => $ownerId,
            'activity_date' => $date,
            'streak_days' => 1,
            'xp_awarded' => 500,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }

    private function authenticateAs(User $user): void
    {
        if ($user->inGroup('student') && ! $user->inGroup('admin')) {
            $program = (new App\Services\AcademicProgramCatalog())->all()[0];
            db_connect()->table('users')->where('id', (int) $user->id)->update([
                'academic_program_id' => $program['id'], 'academic_program_title' => $program['title'], 'academic_program_selected_at' => '2026-11-01 00:00:00',
            ]);
        }
        $this->actingAs($user);
        $this->withSession($_SESSION);
    }

    private function getAuthenticated(string $path): TestResponse
    {
        $this->withSession($_SESSION);
        $response = $this->get($path);
        $this->withSession($_SESSION);

        return $response;
    }
}
