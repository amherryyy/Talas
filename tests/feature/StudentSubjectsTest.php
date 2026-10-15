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
final class StudentSubjectsTest extends CIUnitTestCase
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

    public function testStudentCanCreateViewEditAndDeleteOwnSubjectWithEscapedOutput(): void
    {
        $student = $this->createUser('student-crud', 'student');
        $this->authenticateAs($student);

        $newForm = $this->getAuthenticated('student/subjects/new');
        $newForm->assertOK();
        $this->assertStringContainsString('name="csrf_test_name"', $newForm->response()->getBody());

        $this->postAuthenticated('student/subjects', [
            'name' => '<script>alert(1)</script>',
        ])->assertRedirect();

        $subjects = new App\Models\SubjectModel();
        $this->assertCount(1, $subjects->findAllForOwner((int) $student->id));
        $subject = $subjects->findAllForOwner((int) $student->id)[0];

        $show = $this->getAuthenticated('student/subjects/' . $subject['id']);
        $show->assertOK();
        $show->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;');
        $show->assertDontSee('<script>alert(1)</script>');

        $this->postAuthenticated('student/subjects/' . $subject['id'], [
            'name' => 'Algebra',
        ])->assertRedirect();

        $this->getAuthenticated('student/subjects/' . $subject['id'])
            ->assertSee('Algebra');

        $this->postAuthenticated('student/subjects/' . $subject['id'] . '/delete')
            ->assertRedirect();

        $this->assertCount(0, $subjects->findAllForOwner((int) $student->id));
    }

    public function testInvalidSubjectNameIsRejectedByTheServer(): void
    {
        $student = $this->createUser('student-invalid', 'student');
        $this->authenticateAs($student);

        $response = $this->postAuthenticated('student/subjects', [
            'name' => str_repeat('x', 121),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('errors');
        $this->assertSame([], (new App\Models\SubjectModel())->findAllForOwner((int) $student->id));
    }

    public function testSubjectSearchIsOwnerScopedAndKeepsSearchTextOutOfTheUrl(): void
    {
        $owner = $this->createUser('subject-search-owner', 'student');
        $otherStudent = $this->createUser('subject-search-other', 'student');
        $subjects = new App\Models\SubjectModel();
        $ownedId = $subjects->insert(['user_id' => $owner->id, 'name' => 'Biology lab'], true);
        $foreignId = $subjects->insert(['user_id' => $otherStudent->id, 'name' => 'Biology seminar'], true);
        $this->assertNotFalse($ownedId);
        $this->assertNotFalse($foreignId);

        $this->authenticateAs($owner);
        $response = $this->postAuthenticated('student/subjects/search', ['query' => 'Biology']);

        $response->assertOK();
        $response->assertSee('Biology lab');
        $response->assertDontSee('Biology seminar');
        $this->assertSame('', $response->response()->getHeaderLine('Location'));
    }

    public function testAnotherStudentCannotReadEditUpdateOrDeleteSubject(): void
    {
        $owner = $this->createUser('student-owner', 'student');
        $otherStudent = $this->createUser('student-other', 'student');
        $subjects = new App\Models\SubjectModel();
        $subjectId = $subjects->insert([
            'user_id' => $owner->id,
            'name'    => 'Owner private subject',
        ], true);
        $this->assertNotFalse($subjectId);

        $this->authenticateAs($otherStudent);

        foreach ([
            'student/subjects/' . $subjectId,
            'student/subjects/' . $subjectId . '/edit',
        ] as $path) {
            $this->assertNotFound(fn () => $this->getAuthenticated($path));
        }

        $this->assertNotFound(fn () => $this->postAuthenticated('student/subjects/' . $subjectId, ['name' => 'Tampered']));
        $this->assertNotFound(fn () => $this->postAuthenticated('student/subjects/' . $subjectId . '/delete'));

        $stored = $subjects->findForOwner((int) $subjectId, (int) $owner->id);
        $this->assertNotNull($stored);
        $this->assertSame('Owner private subject', $stored['name']);
    }

    public function testAdminCannotUseStudentSubjectRoutes(): void
    {
        $admin = $this->createUser('admin-subject-denial', 'admin');
        $this->authenticateAs($admin);

        $response = $this->getAuthenticated('student/subjects');

        $this->assertSame(403, $response->response()->getStatusCode());
    }

    public function testSignedOutUserCannotUseStudentSubjectRoutes(): void
    {
        $response = $this->get('student/subjects');

        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    public function testStateChangingSubjectRequestWithoutCsrfTokenIsRejected(): void
    {
        $student = $this->createUser('student-csrf', 'student');
        $this->authenticateAs($student);

        try {
            $this->post('student/subjects', ['name' => 'No token']);
            $this->fail('A state-changing request without a CSRF token must be rejected.');
        } catch (SecurityException) {
            $this->assertTrue(true);
        }

        $this->assertSame([], (new App\Models\SubjectModel())->findAllForOwner((int) $student->id));
    }

    private function assertNotFound(callable $request): void
    {
        try {
            $request();
            $this->fail('A foreign subject must be indistinguishable from a missing subject.');
        } catch (PageNotFoundException) {
            $this->assertTrue(true);
        }
    }

    private function createUser(string $username, string $group): User
    {
        $userModel = new UserModel();
        $user      = new User([
            'username' => $username,
            'email'    => $username . '@example.test',
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
