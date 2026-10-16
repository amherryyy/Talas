<?php

use App\Models\StudySessionModel;
use App\Models\SubjectModel;
use App\Models\TopicModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Security\Exceptions\SecurityException;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use Config\Database;

/**
 * @internal
 */
final class StudentStudySessionsTest extends CIUnitTestCase
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

    public function testStudentCanSaveEditAndDeleteOwnSession(): void
    {
        $student = $this->createUser('session-crud', 'student');
        $subjectId = $this->createSubject((int) $student->id, 'Biology');
        $topicId = $this->createTopic($subjectId, 'Cell structure');
        $this->authenticateAs($student);

        $page = $this->getAuthenticated('student/sessions');
        $page->assertOK();
        $page->assertSee('Time a session');
        $this->postAuthenticated('student/sessions', [
            'subject_id' => (string) $subjectId,
            'topic_id' => (string) $topicId,
            'duration_minutes' => '35',
        ])->assertRedirect();

        $sessions = new StudySessionModel();
        $saved = $sessions->findAllForOwner((int) $student->id);
        $this->assertCount(1, $saved);
        $sessionId = (int) $saved[0]['id'];
        $originalCompletedAt = $saved[0]['completed_at'];
        $this->assertSame('Biology', $saved[0]['subject_name']);
        $this->assertSame('Cell structure', $saved[0]['topic_title']);
        $this->assertSame(35, (int) $saved[0]['duration_minutes']);

        $this->postAuthenticated('student/sessions/' . $sessionId, [
            'subject_id' => (string) $subjectId,
            'topic_id' => '',
            'duration_minutes' => '50',
        ])->assertRedirect();
        $updated = $sessions->findForOwner($sessionId, (int) $student->id);
        $this->assertSame(50, (int) $updated['duration_minutes']);
        $this->assertNull($updated['topic_id']);
        $this->assertSame($originalCompletedAt, $updated['completed_at']);

        $confirmation = $this->postAuthenticated('student/sessions/' . $sessionId . '/delete');
        $confirmation->assertOK();
        $confirmation->assertSee('Delete study session?');
        $this->postAuthenticated('student/sessions/' . $sessionId . '/delete/confirm', ['confirm_delete' => '1'])->assertRedirect();
        $this->assertNull($sessions->findForOwner($sessionId, (int) $student->id));
    }

    public function testDurationAndSubjectTopicRelationshipsAreValidatedBeforeSave(): void
    {
        $owner = $this->createUser('session-validation', 'student');
        $other = $this->createUser('session-validation-other', 'student');
        $subjectId = $this->createSubject((int) $owner->id);
        $otherSubjectId = $this->createSubject((int) $other->id, 'Private subject');
        $foreignTopicId = $this->createTopic($otherSubjectId, 'Private topic');
        $this->authenticateAs($owner);

        foreach ([
            ['subject_id' => (string) $subjectId, 'topic_id' => '', 'duration_minutes' => '0'],
            ['subject_id' => (string) $subjectId, 'topic_id' => '', 'duration_minutes' => '481'],
            ['subject_id' => (string) $otherSubjectId, 'topic_id' => '', 'duration_minutes' => '25'],
            ['subject_id' => (string) $subjectId, 'topic_id' => (string) $foreignTopicId, 'duration_minutes' => '25'],
        ] as $input) {
            $this->postAuthenticated('student/sessions', $input)->assertRedirect();
        }

        $this->assertSame([], (new StudySessionModel())->findAllForOwner((int) $owner->id));
    }

    public function testStudentCannotReadOrChangeAnotherOwnersSession(): void
    {
        $owner = $this->createUser('session-private-owner', 'student');
        $other = $this->createUser('session-private-other', 'student');
        $subjectId = $this->createSubject((int) $other->id, 'Private biology');
        $sessionId = (new StudySessionModel())->insert([
            'user_id' => (int) $other->id,
            'subject_id' => $subjectId,
            'topic_id' => null,
            'duration_minutes' => 30,
            'completed_at' => '2026-10-16 10:00:00',
        ], true);
        $this->authenticateAs($owner);

        $list = $this->getAuthenticated('student/sessions');
        $list->assertOK();
        $list->assertDontSee('Private biology');
        $this->assertNotFound(fn () => $this->getAuthenticated('student/sessions/' . $sessionId . '/edit'));
        $this->assertNotFound(fn () => $this->postAuthenticated('student/sessions/' . $sessionId, [
            'subject_id' => (string) $subjectId,
            'topic_id' => '',
            'duration_minutes' => '45',
        ]));
        $this->assertNotFound(fn () => $this->postAuthenticated('student/sessions/' . $sessionId . '/delete/confirm', ['confirm_delete' => '1']));
        $this->assertSame(30, (int) (new StudySessionModel())->find($sessionId)['duration_minutes']);
    }

    public function testSubjectDeletionIsBlockedUntilReferencingSessionIsRemoved(): void
    {
        $student = $this->createUser('session-subject-delete', 'student');
        $subjectId = $this->createSubject((int) $student->id);
        $topicId = $this->createTopic($subjectId, 'Saved topic');
        $sessionId = (new StudySessionModel())->insert([
            'user_id' => (int) $student->id,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'duration_minutes' => 25,
            'completed_at' => '2026-10-16 10:00:00',
        ], true);
        $this->authenticateAs($student);

        $this->postAuthenticated('student/subjects/' . $subjectId . '/delete')->assertRedirect();
        $this->assertNotNull((new SubjectModel())->findForOwner($subjectId, (int) $student->id));

        $this->postAuthenticated('student/sessions/' . $sessionId . '/delete/confirm', ['confirm_delete' => '1'])->assertRedirect();
        $this->postAuthenticated('student/subjects/' . $subjectId . '/delete')->assertRedirect();
        $this->assertNotNull((new SubjectModel())->findForOwner($subjectId, (int) $student->id));
        $this->postAuthenticated('student/subjects/' . $subjectId . '/topics/' . $topicId . '/delete')->assertRedirect();
        $this->postAuthenticated('student/subjects/' . $subjectId . '/delete')->assertRedirect();
        $this->assertNull((new SubjectModel())->findForOwner($subjectId, (int) $student->id));
    }

    public function testSessionRoutesRequireStudentRoleAndCsrf(): void
    {
        $admin = $this->createUser('session-admin', 'admin');
        $this->authenticateAs($admin);
        $this->getAuthenticated('student/sessions')->assertStatus(403);
        $this->postAuthenticated('student/sessions', ['subject_id' => '1', 'topic_id' => '', 'duration_minutes' => '25'])->assertStatus(403);

        auth()->logout();
        $response = $this->get('student/sessions');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    public function testSessionCreationRequiresCsrf(): void
    {
        $student = $this->createUser('session-csrf', 'student');
        $this->authenticateAs($student);
        try {
            $this->post('student/sessions', ['subject_id' => '1', 'topic_id' => '', 'duration_minutes' => '25']);
            $this->fail('Saving a study session without a CSRF token must be rejected.');
        } catch (SecurityException) {
            $this->assertTrue(true);
        }
        $this->assertSame([], (new StudySessionModel())->findAllForOwner((int) $student->id));
    }

    private function createSubject(int $ownerId, string $name = 'Synthetic subject'): int
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

    private function assertNotFound(callable $request): void
    {
        try {
            $request();
            $this->fail('A foreign session must be indistinguishable from a missing record.');
        } catch (PageNotFoundException) {
            $this->assertTrue(true);
        }
    }
}
