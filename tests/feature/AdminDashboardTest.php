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

/**
 * @internal
 */
final class AdminDashboardTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
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
        ], $summary);

        $this->authenticateAs($admin);
        $page = $this->getAuthenticated('admin');
        $page->assertOK();
        $page->assertSee('duno.');
        $page->assertSee('Student accounts');
        $page->assertSee('Subjects');
        $page->assertSee('Topics');
        $page->assertSee('Saved study sessions');
        $page->assertSee('Total study minutes');
        $page->assertSee('70');
        $page->assertSee('totals only');
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
        ], (new AdminDashboardSummaryService())->summarize());
    }

    public function testStudentAndSignedOutVisitorsCannotAccessAdminDashboard(): void
    {
        $student = $this->createUser('admin-summary-denied', ['student']);
        $this->authenticateAs($student);
        $this->getAuthenticated('admin')->assertStatus(403);

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

    private function authenticateAs(User $user): void
    {
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
