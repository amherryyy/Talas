<?php

use App\Models\StudySessionModel;
use App\Models\SubjectModel;
use App\Models\TopicModel;
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
final class StudentAralyticsTest extends CIUnitTestCase
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

    public function testReportShowsOnlyTheOwnersSessionsAndExpectedBreakdowns(): void
    {
        $owner = $this->createUser('aralytics-owner');
        $other = $this->createUser('aralytics-other');
        $subjectId = $this->createSubject((int) $owner->id, '<Biology>');
        $topicId = $this->createTopic($subjectId, '<Cells>');
        $otherSubject = $this->createSubject((int) $other->id, 'Private subject');
        $this->createSession((int) $owner->id, $subjectId, $topicId, 35, $this->localDate('today') . ' 09:00:00');
        $this->createSession((int) $owner->id, $subjectId, null, 25, $this->localDate('-1 day') . ' 10:00:00');
        $this->createSession((int) $other->id, $otherSubject, null, 480, $this->localDate('today') . ' 11:00:00');
        $this->authenticateAs($owner);

        $response = $this->getAuthenticated('student/aralytics?period=7_days');
        $response->assertOK();
        $response->assertSee('Study activity: Last 7 days');
        $response->assertSee('60');
        $response->assertSee('2');
        $response->assertSee('30.0 min');
        $response->assertSee('&lt;Biology&gt;');
        $response->assertSee('&lt;Cells&gt;');
        $response->assertSee('No topic');
        $response->assertDontSee('Private subject');
        $response->assertSee('Print report');
        $response->assertSee('Dated sessions');
        $response->assertSee('65 XP');
        $response->assertSee('60 study + 5 streak');
        $response->assertDontSee('545 XP');
    }

    public function testProgressionRecalculatesFromCurrentSavedSessionRecords(): void
    {
        $student = $this->createUser('aralytics-progression');
        $subjectId = $this->createSubject((int) $student->id, 'Biology');
        $sessionId = $this->createSession((int) $student->id, $subjectId, null, 114, $this->localDate('today') . ' 09:00:00');
        $this->authenticateAs($student);

        $before = $this->getAuthenticated('student/aralytics');
        $before->assertOK();
        $before->assertSee('119 XP');
        $before->assertSee('Level 1');

        $sessions = new StudySessionModel();
        $this->assertTrue($sessions->updateForOwner($sessionId, (int) $student->id, $subjectId, null, 115));
        $afterUpdate = $this->getAuthenticated('student/aralytics');
        $afterUpdate->assertOK();
        $afterUpdate->assertSee('120 XP');
        $afterUpdate->assertSee('Level 2');
        $afterUpdate->assertSee('30-day study rhythm');

        $this->assertTrue($sessions->deleteForOwner($sessionId, (int) $student->id));
        $afterDelete = $this->getAuthenticated('student/aralytics');
        $afterDelete->assertOK();
        $afterDelete->assertSee('5 XP');
        $afterDelete->assertSee('Level 1');
    }

    public function testSelectedTodayAndAllTimePeriodsAndInvalidPeriodFallback(): void
    {
        $student = $this->createUser('aralytics-periods');
        $subjectId = $this->createSubject((int) $student->id, 'History');
        $this->createSession((int) $student->id, $subjectId, null, 20, $this->localDate('today') . ' 08:00:00');
        $this->createSession((int) $student->id, $subjectId, null, 40, $this->localDate('-30 days') . ' 08:00:00');
        $this->authenticateAs($student);

        $today = $this->getAuthenticated('student/aralytics?period=today');
        $today->assertOK();
        $today->assertSee('Study activity: Today');
        $today->assertSee('20');

        $allTime = $this->getAuthenticated('student/aralytics?period=all_time');
        $allTime->assertOK();
        $allTime->assertSee('Study activity: All time');
        $allTime->assertSee('60');

        $invalid = $this->getAuthenticated('student/aralytics?period=unexpected');
        $invalid->assertOK();
        $invalid->assertSee('Study activity: Last 7 days');
    }

    public function testReportRoutesRequireStudentRole(): void
    {
        $admin = $this->createUser('aralytics-admin', 'admin');
        $this->authenticateAs($admin);
        $this->getAuthenticated('student/aralytics')->assertStatus(403);

        auth()->logout();
        $response = $this->get('student/aralytics');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    private function createSubject(int $ownerId, string $name): int
    {
        return (int) (new SubjectModel())->insert(['user_id' => $ownerId, 'name' => $name], true);
    }

    private function createTopic(int $subjectId, string $title): int
    {
        return (int) (new TopicModel())->insert([
            'subject_id' => $subjectId,
            'title' => $title,
            'due_date' => null,
            'importance' => 'medium',
            'progress' => 'not_started',
        ], true);
    }

    private function createSession(int $ownerId, int $subjectId, ?int $topicId, int $minutes, string $completedAt): int
    {
        return (int) (new StudySessionModel())->insert([
            'user_id' => $ownerId,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'duration_minutes' => $minutes,
            'completed_at' => $completedAt,
        ], true);
    }

    private function createUser(string $username, string $group = 'student'): User
    {
        $model = new UserModel();
        $user = new User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'Synthetic-test-password-123!',
        ]);
        $userId = $model->insert($user);
        $created = $model->findById((int) $userId);
        $created->addGroup($group);

        return $created;
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

    private function localDate(string $relative): string
    {
        return (new DateTimeImmutable($relative, new DateTimeZone(config('App')->appTimezone)))->format('Y-m-d');
    }
}
