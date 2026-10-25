<?php

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
final class StudentTopicsTest extends CIUnitTestCase
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

    public function testStudentCanCreateViewUpdateAndDeleteTopicWithDefaultsAndEscapedTitle(): void
    {
        $student = $this->createUser('topic-crud', 'student');
        $this->authenticateAs($student);
        $subjectId = $this->createSubject((int) $student->id);

        $this->getAuthenticated('student/subjects/' . $subjectId . '/topics/new')->assertOK();
        $this->postAuthenticated('student/subjects/' . $subjectId . '/topics', [
            'title'      => '<script>alert(1)</script>',
            'due_date'   => '',
            'importance' => 'medium',
            'progress'   => 'not_started',
        ])->assertRedirect();

        $topics = new App\Models\TopicModel();
        $stored = $topics->findAllForSubjectOwner($subjectId, (int) $student->id);
        $this->assertCount(1, $stored);
        $topicId = (int) $stored[0]['id'];
        $this->assertNull($stored[0]['due_date']);
        $this->assertSame('medium', $stored[0]['importance']);
        $this->assertSame('not_started', $stored[0]['progress']);

        $subjectPage = $this->getAuthenticated('student/subjects/' . $subjectId);
        $subjectPage->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;');
        $subjectPage->assertDontSee('<script>alert(1)</script>');
        $topicPage = $this->getAuthenticated('student/subjects/' . $subjectId . '/topics/' . $topicId);
        $topicPage->assertSee('No due date');
        $topicPage->assertSee('Medium');
        $topicPage->assertSee('Not Started');

        $this->postAuthenticated('student/subjects/' . $subjectId . '/topics/' . $topicId, [
            'title'      => 'Systems of equations',
            'due_date'   => '2026-01-10',
            'importance' => 'high',
            'progress'   => 'in_progress',
        ])->assertRedirect();

        $updated = $topics->findForSubjectOwner($subjectId, $topicId, (int) $student->id);
        $this->assertNotNull($updated);
        $this->assertSame('Systems of equations', $updated['title']);
        $this->assertSame('2026-01-10', $updated['due_date']);
        $this->assertSame('high', $updated['importance']);
        $this->assertSame('in_progress', $updated['progress']);

        $this->postAuthenticated('student/subjects/' . $subjectId . '/topics/' . $topicId . '/delete')->assertRedirect();
        $this->assertNull($topics->findForSubjectOwner($subjectId, $topicId, (int) $student->id));
    }

    public function testTopicSearchMatchesSubjectOrTitleAndNeverShowsAnotherOwnersData(): void
    {
        $owner = $this->createUser('topic-search-owner', 'student');
        $otherStudent = $this->createUser('topic-search-other', 'student');
        $mathSubjectId = $this->createSubject((int) $owner->id, 'Discrete Mathematics');
        $biologySubjectId = $this->createSubject((int) $owner->id, 'Biology');
        $foreignSubjectId = $this->createSubject((int) $otherStudent->id, 'Discrete Mathematics Private');
        $this->insertTopic($mathSubjectId, 'Graph theory');
        $this->insertTopic($mathSubjectId, 'Linear functions');
        $this->insertTopic($biologySubjectId, 'Cell division');
        $this->insertTopic($foreignSubjectId, 'Foreign graph theory');

        $this->authenticateAs($owner);
        $topicSearchPage = $this->getAuthenticated('student/topics');
        $topicSearchPage->assertOK();
        $topicSearchPage->assertSee('Search topics');
        $topicSearchPage->assertSee('Discrete Mathematics');
        $topicSearchPage->assertDontSee('Discrete Mathematics Private');
        $searchBySubject = $this->postAuthenticated('student/topics', ['query' => 'Discrete Mathematics']);
        $searchBySubject->assertOK();
        $this->assertSame('', $searchBySubject->response()->getHeaderLine('Location'));
        $searchBySubject->assertSee('Graph theory');
        $searchBySubject->assertSee('Linear functions');
        $searchBySubject->assertDontSee('Cell division');
        $searchBySubject->assertDontSee('Foreign graph theory');

        $searchByTitle = $this->postAuthenticated('student/topics', ['query' => 'Cell division']);
        $searchByTitle->assertOK();
        $searchByTitle->assertSee('Cell division');
        $searchByTitle->assertDontSee('Foreign graph theory');

        $noMatches = $this->postAuthenticated('student/topics', ['query' => 'No matching record']);
        $noMatches->assertOK();
        $noMatches->assertSee('No topics match these filters.');
    }

    public function testTopicFiltersCombineAndDueDateStatesExcludeCompletedWork(): void
    {
        $student = $this->createUser('topic-filter-student', 'student');
        $mathSubjectId = $this->createSubject((int) $student->id, 'Mathematics');
        $biologySubjectId = $this->createSubject((int) $student->id, 'Biology');
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $this->insertTopic($mathSubjectId, 'Past due math', $yesterday, 'high', 'in_progress');
        $this->insertTopic($mathSubjectId, 'Due today math', $today, 'medium', 'in_progress');
        $this->insertTopic($mathSubjectId, 'Future math', $tomorrow, 'medium', 'not_started');
        $this->insertTopic($biologySubjectId, 'Completed past topic', $yesterday, 'low', 'done');
        $this->insertTopic($biologySubjectId, 'No date biology', null, 'low', 'not_started');
        $this->authenticateAs($student);

        $overdue = $this->postAuthenticated('student/topics', ['due_state' => 'overdue']);
        $overdue->assertOK();
        $overdue->assertSee('Past due math');
        $overdue->assertDontSee('Due today math');
        $overdue->assertDontSee('Future math');
        $overdue->assertDontSee('Completed past topic');

        $upcoming = $this->postAuthenticated('student/topics', ['due_state' => 'upcoming']);
        $upcoming->assertOK();
        $upcoming->assertSee('Future math');
        $upcoming->assertSee('Due today math');
        $upcoming->assertDontSee('Past due math');

        $combined = $this->postAuthenticated('student/topics', [
            'subject_id' => (string) $biologySubjectId,
            'importance' => 'low',
            'progress' => 'not_started',
            'due_state' => 'undated',
        ]);
        $combined->assertOK();
        $combined->assertSee('No date biology');
        $combined->assertDontSee('Completed past topic');
    }

    public function testPriorityPageRanksOnlyOwnedUnfinishedTopicsAndExplainsScores(): void
    {
        $owner = $this->createUser('priority-owner', 'student');
        $otherStudent = $this->createUser('priority-other', 'student');
        $subjectId = $this->createSubject((int) $owner->id, 'Priority mathematics');
        $foreignSubjectId = $this->createSubject((int) $otherStudent->id, 'Private subject');
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $inSevenDays = date('Y-m-d', strtotime('+7 days'));
        $inEightDays = date('Y-m-d', strtotime('+8 days'));
        $this->insertTopic($subjectId, 'Later high importance', $inEightDays, 'high', 'not_started');
        $this->insertTopic($subjectId, 'Today low importance', $today, 'low', 'not_started');
        $this->insertTopic($subjectId, 'Overdue low importance', $yesterday, 'low', 'in_progress');
        $this->insertTopic($subjectId, 'Soon medium importance', $inSevenDays, 'medium', 'in_progress');
        $this->insertTopic($subjectId, 'Undated high importance', null, 'high', 'not_started');
        $this->insertTopic($subjectId, 'Completed topic', $yesterday, 'high', 'done');
        $this->insertTopic($foreignSubjectId, 'Foreign unfinished topic', $yesterday, 'high', 'not_started');
        $this->authenticateAs($owner);

        $page = $this->getAuthenticated('student/priority');
        $page->assertOK();
        $page->assertSee('Duno Focus');
        $page->assertSeeInOrder([
            'Today low importance',
            'Later high importance',
            'Overdue low importance',
            'Soon medium importance',
            'Undated high importance',
        ]);
        $page->assertDontSee('Completed topic');
        $page->assertDontSee('Foreign unfinished topic');
        $page->assertSee('Score 5');
        $page->assertSee('Due today (+3)');
        $page->assertSee('Not started (+1)');
        $page->assertSee('Overdue (+3)');
        $page->assertSee('Due within 7 days (+2)');
        $page->assertSee('No due date (+0)');
    }

    public function testPriorityPageShowsAnEmptyStateWithoutUnfinishedTopics(): void
    {
        $student = $this->createUser('priority-empty', 'student');
        $subjectId = $this->createSubject((int) $student->id);
        $this->insertTopic($subjectId, 'Only completed item', null, 'medium', 'done');
        $this->authenticateAs($student);

        $page = $this->getAuthenticated('student/priority');
        $page->assertOK();
        $page->assertSee('No unfinished topics are on your list right now.');
        $page->assertDontSee('Only completed item');
    }

    public function testInvalidTopicTitleDateImportanceAndProgressAreRejected(): void
    {
        $student = $this->createUser('topic-invalid', 'student');
        $this->authenticateAs($student);
        $subjectId = $this->createSubject((int) $student->id);

        foreach ([
            ['title' => '', 'due_date' => '', 'importance' => 'medium', 'progress' => 'not_started'],
            ['title' => str_repeat('x', 121), 'due_date' => '', 'importance' => 'medium', 'progress' => 'not_started'],
            ['title' => 'Valid', 'due_date' => '2026-02-30', 'importance' => 'medium', 'progress' => 'not_started'],
            ['title' => 'Valid', 'due_date' => '', 'importance' => 'urgent', 'progress' => 'not_started'],
            ['title' => 'Valid', 'due_date' => '', 'importance' => 'medium', 'progress' => 'blocked'],
        ] as $invalidTopic) {
            $response = $this->postAuthenticated('student/subjects/' . $subjectId . '/topics', $invalidTopic);
            $response->assertRedirect();
            $response->assertSessionHas('errors');
        }

        $this->assertSame([], (new App\Models\TopicModel())->findAllForSubjectOwner($subjectId, (int) $student->id));
    }

    public function testOwnerScopeBlocksForeignParentAndForeignTopicIds(): void
    {
        $owner = $this->createUser('topic-owner', 'student');
        $otherStudent = $this->createUser('topic-other', 'student');
        $ownerSubjectId = $this->createSubject((int) $owner->id, 'Private subject');
        $foreignSubjectId = $this->createSubject((int) $otherStudent->id, 'Foreign subject');
        $topics = new App\Models\TopicModel();
        $foreignTopicId = $topics->insert([
            'subject_id' => $foreignSubjectId,
            'title' => 'Foreign topic',
            'due_date' => null,
            'importance' => 'medium',
            'progress' => 'not_started',
        ], true);
        $ownTopicId = $topics->insert([
            'subject_id' => $ownerSubjectId,
            'title' => 'Own topic',
            'due_date' => null,
            'importance' => 'medium',
            'progress' => 'not_started',
        ], true);
        $this->assertNotFalse($foreignTopicId);
        $this->assertNotFalse($ownTopicId);

        $this->authenticateAs($owner);
        foreach ([
            'student/subjects/' . $foreignSubjectId . '/topics',
            'student/subjects/' . $foreignSubjectId . '/topics/' . $foreignTopicId,
            'student/subjects/' . $ownerSubjectId . '/topics/' . $foreignTopicId,
        ] as $path) {
            $this->assertNotFound(fn () => $this->getAuthenticated($path));
        }

        $this->assertNotFound(fn () => $this->postAuthenticated(
            'student/subjects/' . $ownerSubjectId . '/topics/' . $foreignTopicId,
            ['title' => 'Tampered', 'due_date' => '', 'importance' => 'high', 'progress' => 'done'],
        ));
        $this->assertNotFound(fn () => $this->postAuthenticated(
            'student/subjects/' . $ownerSubjectId . '/topics/' . $foreignTopicId . '/delete',
        ));
        $this->assertSame('Foreign topic', $topics->findForSubjectOwner($foreignSubjectId, (int) $foreignTopicId, (int) $otherStudent->id)['title']);
        $this->assertSame('Own topic', $topics->findForSubjectOwner($ownerSubjectId, (int) $ownTopicId, (int) $owner->id)['title']);
    }

    public function testSavedSessionReferenceBlocksTopicDeletion(): void
    {
        $student = $this->createUser('topic-session', 'student');
        $this->authenticateAs($student);
        $subjectId = $this->createSubject((int) $student->id);
        $topicId = (new App\Models\TopicModel())->insert([
            'subject_id' => $subjectId,
            'title' => 'Referenced topic',
            'due_date' => null,
            'importance' => 'medium',
            'progress' => 'not_started',
        ], true);
        $this->assertNotFalse($topicId);

        (new App\Models\StudySessionModel())->insert([
            'user_id' => (int) $student->id,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'duration_minutes' => 25,
            'completed_at' => '2026-10-16 10:00:00',
        ]);

        $deleteResponse = $this->postAuthenticated('student/subjects/' . $subjectId . '/topics/' . $topicId . '/delete');
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('error');
        $this->assertNotNull((new App\Models\TopicModel())->findForSubjectOwner(
            $subjectId,
            (int) $topicId,
            (int) $student->id,
        ));
    }

    public function testAdminAndSignedOutUsersCannotUseTopicRoutes(): void
    {
        $admin = $this->createUser('topic-admin', 'admin');
        $this->authenticateAs($admin);
        $this->assertSame(403, $this->getAuthenticated('student/priority')->response()->getStatusCode());
        $this->assertSame(403, $this->getAuthenticated('student/topics')->response()->getStatusCode());
        $this->assertSame(403, $this->getAuthenticated('student/subjects/1/topics')->response()->getStatusCode());

        auth()->logout();
        $priorityResponse = $this->get('student/priority');
        $this->assertContains($priorityResponse->response()->getStatusCode(), [302, 403]);
        $globalResponse = $this->get('student/topics');
        $this->assertContains($globalResponse->response()->getStatusCode(), [302, 403]);
        $response = $this->get('student/subjects/1/topics');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    public function testGlobalTopicSearchRequiresCsrf(): void
    {
        $student = $this->createUser('topic-search-csrf', 'student');
        $this->authenticateAs($student);

        try {
            $this->post('student/topics', ['query' => 'private title']);
            $this->fail('A search POST without a CSRF token must be rejected.');
        } catch (SecurityException) {
            $this->assertTrue(true);
        }
    }

    public function testStateChangingTopicRequestWithoutCsrfTokenIsRejected(): void
    {
        $student = $this->createUser('topic-csrf', 'student');
        $this->authenticateAs($student);
        $subjectId = $this->createSubject((int) $student->id);

        try {
            $this->post('student/subjects/' . $subjectId . '/topics', [
                'title' => 'No token',
                'due_date' => '',
                'importance' => 'medium',
                'progress' => 'not_started',
            ]);
            $this->fail('A state-changing request without a CSRF token must be rejected.');
        } catch (SecurityException) {
            $this->assertTrue(true);
        }

        $this->assertSame([], (new App\Models\TopicModel())->findAllForSubjectOwner($subjectId, (int) $student->id));
    }

    private function createSubject(int $ownerId, string $name = 'Synthetic subject'): int
    {
        $subjectId = (new App\Models\SubjectModel())->insert([
            'user_id' => $ownerId,
            'name' => $name,
        ], true);
        $this->assertNotFalse($subjectId);

        return (int) $subjectId;
    }

    private function insertTopic(int $subjectId, string $title, ?string $dueDate = null, string $importance = 'medium', string $progress = 'not_started'): int
    {
        $topicId = (new App\Models\TopicModel())->insert([
            'subject_id' => $subjectId,
            'title' => $title,
            'due_date' => $dueDate,
            'importance' => $importance,
            'progress' => $progress,
        ], true);
        $this->assertNotFalse($topicId);

        return (int) $topicId;
    }

    private function assertNotFound(callable $request): void
    {
        try {
            $request();
            $this->fail('A foreign topic or subject must be indistinguishable from a missing record.');
        } catch (PageNotFoundException) {
            $this->assertTrue(true);
        }
    }

    private function createUser(string $username, string $group): User
    {
        $userModel = new UserModel();
        $user = new User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'Synthetic-test-password-123!',
        ]);
        $userId = $userModel->insert($user);
        $this->assertNotFalse($userId);

        $createdUser = $userModel->findById((int) $userId);
        $this->assertInstanceOf(User::class, $createdUser);
        $createdUser->addGroup($group);

        return $createdUser;
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
