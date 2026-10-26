<?php

use App\Models\StudentStreakModel;
use App\Services\StudentStreakService;
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
final class StudentStreakTest extends CIUnitTestCase
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

    public function testStudentGetsOneFiveXpRewardPerCalendarDayOnAuthenticatedVisit(): void
    {
        $student = $this->createUser('streak-daily-visit', 'student');
        $this->authenticateAs($student);

        $firstVisit = $this->getAuthenticated('student');
        $firstVisit->assertOK();
        $firstVisit->assertSee('Daily streak reward: +5 XP');
        $firstVisit->assertSee('1-day streak');
        $firstVisit->assertSee('class="streak-flame is-new"');

        $repeatVisit = $this->getAuthenticated('student/plan');
        $repeatVisit->assertOK();
        $repeatVisit->assertDontSee('Daily streak reward: +5 XP');

        $rows = (new StudentStreakModel())->where('user_id', (int) $student->id)->findAll();
        $this->assertCount(1, $rows);
        $this->assertSame(5, (new StudentStreakModel())->totalXpForOwner((int) $student->id));
    }

    public function testStreakContinuesAcrossCalendarDaysAndResetsAfterAMissedDay(): void
    {
        $student = $this->createUser('streak-date-rules', 'student');
        $timezone = new DateTimeZone('Asia/Manila');
        $service = new StudentStreakService();

        $first = $service->recordVisit((int) $student->id, new DateTimeImmutable('2026-10-01 23:50:00', $timezone), $timezone);
        $nextDay = $service->recordVisit((int) $student->id, new DateTimeImmutable('2026-10-02 00:10:00', $timezone), $timezone);
        $missedDay = $service->recordVisit((int) $student->id, new DateTimeImmutable('2026-10-04 08:00:00', $timezone), $timezone);

        $this->assertTrue($first['awarded']);
        $this->assertSame(1, $first['streak_days']);
        $this->assertTrue($nextDay['awarded']);
        $this->assertSame(2, $nextDay['streak_days']);
        $this->assertTrue($missedDay['awarded']);
        $this->assertSame(1, $missedDay['streak_days']);
        $this->assertSame(15, (new StudentStreakModel())->totalXpForOwner((int) $student->id));
    }

    public function testStreakRecordsBelongToTheirStudentAndAdminsDoNotEarnVisitXp(): void
    {
        $student = $this->createUser('streak-owner', 'student');
        $otherStudent = $this->createUser('streak-other-owner', 'student');
        $admin = $this->createUser('streak-admin', 'admin');
        $timezone = new DateTimeZone('Asia/Manila');
        $service = new StudentStreakService();
        $now = new DateTimeImmutable('2026-10-03 12:00:00', $timezone);
        $service->recordVisit((int) $student->id, $now, $timezone);

        $this->assertSame(5, (new StudentStreakModel())->totalXpForOwner((int) $student->id));
        $this->assertSame(0, (new StudentStreakModel())->totalXpForOwner((int) $otherStudent->id));

        $this->authenticateAs($admin);
        $this->getAuthenticated('admin')->assertOK();
        $this->assertSame(0, (new StudentStreakModel())->totalXpForOwner((int) $admin->id));
    }

    private function createUser(string $username, string $group): User
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
}
