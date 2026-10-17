<?php

use App\Models\StudyPlanModel;
use App\Models\StudyPlanItemModel;
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
final class StudentPlansTest extends CIUnitTestCase
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

    public function testStudentGeneratesAndAdjustsAPlanWithoutExceedingAvailableTime(): void
    {
        $student = $this->createUser('plan-owner', 'student');
        $subjectId = $this->createSubject((int) $student->id, 'Biology');
        $firstTopic = $this->insertTopic($subjectId, 'Cell structure', 'high', 'not_started');
        $secondTopic = $this->insertTopic($subjectId, 'Photosynthesis', 'medium', 'in_progress');
        $thirdTopic = $this->insertTopic($subjectId, 'Genetics', 'low', 'in_progress');
        $this->authenticateAs($student);

        $page = $this->getAuthenticated('student/plan');
        $page->assertOK();
        $page->assertSee('Duno Plan');
        $page->assertSee('Today’s study plan');
        $this->postAuthenticated('student/plan/generate', ['available_minutes' => '60'])->assertRedirect();

        $plan = $this->planFor((int) $student->id);
        $this->assertSame(60, (int) $plan['available_minutes']);
        $this->assertSame([$firstTopic, $secondTopic, $thirdTopic], array_map('intval', array_column($plan['items'], 'topic_id')));
        $this->assertSame([25, 25, 10], array_map('intval', array_column($plan['items'], 'duration_minutes')));

        $this->postAuthenticated('student/plan/items/' . $plan['items'][1]['id'] . '/duration', ['duration_minutes' => '40'])
            ->assertRedirect();
        $unchanged = $this->planFor((int) $student->id);
        $this->assertSame([25, 25, 10], array_map('intval', array_column($unchanged['items'], 'duration_minutes')));

        $this->postAuthenticated('student/plan/items/' . $plan['items'][1]['id'] . '/duration', ['duration_minutes' => '20'])
            ->assertRedirect();
        $this->postAuthenticated('student/plan/items/' . $plan['items'][1]['id'] . '/move/up')->assertRedirect();
        $adjusted = $this->planFor((int) $student->id);
        $this->assertSame([$secondTopic, $firstTopic, $thirdTopic], array_map('intval', array_column($adjusted['items'], 'topic_id')));
        $this->assertSame(55, array_sum(array_map('intval', array_column($adjusted['items'], 'duration_minutes'))));

        $this->postAuthenticated('student/plan/items/' . $adjusted['items'][0]['id'] . '/remove')->assertRedirect();
        $afterRemove = $this->planFor((int) $student->id);
        $this->assertCount(2, $afterRemove['items']);
        $this->assertSame([1, 2], array_map('intval', array_column($afterRemove['items'], 'position')));
    }

    public function testAvailableTimeValidationAndPlanReplacementConfirmation(): void
    {
        $student = $this->createUser('plan-confirm', 'student');
        $subjectId = $this->createSubject((int) $student->id);
        $this->insertTopic($subjectId, 'Review topic', 'medium', 'not_started');
        $this->authenticateAs($student);

        $this->postAuthenticated('student/plan/generate', ['available_minutes' => '481'])->assertRedirect();
        $this->assertNull((new StudyPlanModel())->findForOwnerDate((int) $student->id, $this->today()));

        $this->postAuthenticated('student/plan/generate', ['available_minutes' => '30'])->assertRedirect();
        $confirmation = $this->postAuthenticated('student/plan/generate', ['available_minutes' => '50']);
        $confirmation->assertOK();
        $confirmation->assertSee('Replace today’s plan?');
        $this->assertSame(30, (int) $this->planFor((int) $student->id)['available_minutes']);

        $this->postAuthenticated('student/plan/generate', [
            'available_minutes' => '50',
            'confirm_replacement' => '1',
        ])->assertRedirect();
        $this->assertSame(50, (int) $this->planFor((int) $student->id)['available_minutes']);
    }

    public function testPlanEditsCannotTargetAnotherOwnersPlanItem(): void
    {
        $owner = $this->createUser('plan-private-owner', 'student');
        $other = $this->createUser('plan-private-other', 'student');
        $subjectId = $this->createSubject((int) $other->id);
        $topicId = $this->insertTopic($subjectId, 'Private topic', 'high', 'not_started');
        $planId = (new StudyPlanModel())->insert([
            'user_id' => (int) $other->id,
            'plan_date' => $this->today(),
            'available_minutes' => 25,
        ], true);
        (new StudyPlanItemModel())->insert([
            'plan_id' => $planId,
            'topic_id' => $topicId,
            'topic_title' => 'Private topic',
            'subject_name' => 'Synthetic subject',
            'duration_minutes' => 25,
            'position' => 1,
        ]);
        $privateItem = (new StudyPlanItemModel())->where('plan_id', $planId)->first();
        $this->authenticateAs($owner);

        $ownerPage = $this->getAuthenticated('student/plan');
        $ownerPage->assertOK();
        $ownerPage->assertDontSee('Private topic');
        $this->postAuthenticated('student/plan/items/' . $privateItem['id'] . '/duration', ['duration_minutes' => '5'])->assertRedirect();

        $storedItem = (new StudyPlanItemModel())->find((int) $privateItem['id']);
        $this->assertSame(25, (int) $storedItem['duration_minutes']);
    }

    public function testPlanRoutesRequireStudentRole(): void
    {
        auth()->logout();
        $this->get('/student/plan')->assertRedirect();

        $admin = $this->createUser('plan-admin', 'admin');
        $this->authenticateAs($admin);
        $this->getAuthenticated('student/plan')->assertStatus(403);
        $this->postAuthenticated('student/plan/generate', ['available_minutes' => '25'])->assertStatus(403);
        $this->assertNull((new StudyPlanModel())->findForOwnerDate((int) $admin->id, $this->today()));
    }

    public function testPlanCanBeSavedWithNoUnfinishedTopics(): void
    {
        $student = $this->createUser('plan-empty', 'student');
        $subjectId = $this->createSubject((int) $student->id);
        $this->insertTopic($subjectId, 'Completed topic', 'high', 'done');
        $this->authenticateAs($student);

        $this->postAuthenticated('student/plan/generate', ['available_minutes' => '25'])->assertRedirect();

        $plan = $this->planFor((int) $student->id);
        $this->assertSame(25, (int) $plan['available_minutes']);
        $this->assertSame([], $plan['items']);
    }

    private function planFor(int $ownerId): array
    {
        $plan = (new StudyPlanModel())->findForOwnerDate($ownerId, $this->today());
        $this->assertIsArray($plan);

        return $plan;
    }

    private function today(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone(config('App')->appTimezone)))->format('Y-m-d');
    }

    private function createSubject(int $ownerId, string $name = 'Synthetic subject'): int
    {
        return (int) (new SubjectModel())->insert(['user_id' => $ownerId, 'name' => $name], true);
    }

    private function insertTopic(int $subjectId, string $title, string $importance, string $progress): int
    {
        return (int) (new TopicModel())->insert([
            'subject_id' => $subjectId,
            'title' => $title,
            'due_date' => null,
            'importance' => $importance,
            'progress' => $progress,
        ], true);
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

    /** @param array<string, string> $data */
    private function postAuthenticated(string $path, array $data = []): TestResponse
    {
        $data[csrf_token()] = csrf_hash();
        $this->withSession($_SESSION);
        $response = $this->post($path, $data);
        $this->withSession($_SESSION);

        return $response;
    }
}
