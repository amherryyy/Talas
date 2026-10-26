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

    public function testStudentCanPreviewSourcedChedTemplate(): void
    {
        $student = $this->createUser('ched-preview-student', 'student');
        $this->authenticateAs($student);

        $catalog = $this->getAuthenticated('student/subjects/templates');
        $catalog->assertOK();
        $catalog->assertSee('Start from a CHED template');
        $catalog->assertSee('Nueva Vizcaya State University undergraduate offerings');
        $catalog->assertSee('Bayombong Campus');
        $catalog->assertSee('Bambang Campus');
        $catalog->assertSee('Bachelor of Science in Computer Engineering');
        $catalog->assertSee('Bachelor of Science in Agriculture');
        $catalog->assertSee('Bachelor of Science in Industrial Technology');
        $catalog->assertSee('not the subjects in each school-approved curriculum');
        $catalog->assertSee('Philippine Senior High School subjects');
        $catalog->assertSee('DepEd Order No. 017, s. 2026 - Strengthened SHS Curriculum');
        $catalog->assertSee('Pag-aaral ng Kasaysayan at Lipunang Pilipino');
        $catalog->assertSee('Academic elective');
        $catalog->assertSee('TechPro elective');
        $catalog->assertSee('Grade 12 learners from non-pilot schools continue the previous SHS curriculum');
        $catalog->assertSee('no subject guide under the Sports, Health, and Wellness cluster');
        $catalog->assertSee('Bachelor of Arts in Communication');
        $catalog->assertSee('Bachelor of Science in Accountancy');
        $catalog->assertSee('Bachelor of Science in Business Administration');
        $catalog->assertSee('not a complete list of programs across the Philippines');

        $preview = $this->getAuthenticated('student/subjects/templates?program=ba-communication');
        $preview->assertOK();
        $preview->assertSee('Year 1 · Semester 1');
        $preview->assertSee('Core course in the source');
        $preview->assertSee('Required course slot; choose the course used by your school');
        $preview->assertSee('CMO No. 35, series of 2017');
        $preview->assertSee('Read the program standards');
        $preview->assertSee('opens in a new tab');
        $preview->assertSee('Duno does not assign a school term');

        $accountancy = $this->getAuthenticated('student/subjects/templates?program=bs-accountancy');
        $accountancy->assertOK();
        $accountancy->assertSee('CMO No. 27, series of 2017');
        $accountancy->assertSee('Fundamentals of Accounting I');
        $accountancy->assertSee('General Education Course Slot 1');

        $business = $this->getAuthenticated('student/subjects/templates?program=bs-business-administration');
        $business->assertOK();
        $business->assertSee('CMO No. 17, series of 2017');
        $business->assertSee('Professional Subject 1');
        $business->assertSee('Elective slot');
    }

    public function testStudentCanImportDepedShsSubjectsWithoutReplacingExistingSubjects(): void
    {
        $student = $this->createUser('shs-import-student', 'student');
        $otherStudent = $this->createUser('shs-import-other', 'student');
        $subjects = new App\Models\SubjectModel();
        $existingId = $subjects->insert(['user_id' => $student->id, 'name' => 'general mathematics'], true);
        $foreignId = $subjects->insert(['user_id' => $otherStudent->id, 'name' => 'Private subject'], true);
        $this->assertNotFalse($existingId);
        $this->assertNotFalse($foreignId);

        $this->authenticateAs($student);
        $this->postAuthenticated('student/subjects/shs/import', [
            'subject_ids' => ['general-mathematics', 'general-science', 'business-1-accounting', 'general-science'],
        ])->assertRedirect();

        $owned = $subjects->findAllForOwner((int) $student->id);
        $this->assertEqualsCanonicalizing(['general mathematics', 'General Science', 'Business 1 - Basic Accounting'], array_column($owned, 'name'));
        $this->assertSame(['Private subject'], array_column($subjects->findAllForOwner((int) $otherStudent->id), 'name'));
        $this->getAuthenticated('student/subjects')->assertSee('1 duplicate was skipped');
    }

    public function testTemplateImportPreservesSubjectsAndSkipsCaseInsensitiveDuplicates(): void
    {
        $student = $this->createUser('ched-import-student', 'student');
        $otherStudent = $this->createUser('ched-import-other', 'student');
        $subjects = new App\Models\SubjectModel();
        $existingId = $subjects->insert([
            'user_id' => $student->id,
            'name' => 'ENGLISH (ge)',
        ], true);
        $otherExistingId = $subjects->insert([
            'user_id' => $otherStudent->id,
            'name' => 'Private course',
        ], true);
        $this->assertNotFalse($existingId);
        $this->assertNotFalse($otherExistingId);

        $this->authenticateAs($student);
        $this->postAuthenticated('student/subjects/templates/import', [
            'program_id' => 'ba-communication',
            'course_ids' => ['y1s1-english', 'y1s1-filipino', 'y1s1-filipino'],
        ])->assertRedirect();

        $owned = $subjects->findAllForOwner((int) $student->id);
        $this->assertCount(2, $owned);
        $this->assertSame(['ENGLISH (ge)', 'Filipino (GE)'], array_column($owned, 'name'));
        $this->assertSame(['Private course'], array_column($subjects->findAllForOwner((int) $otherStudent->id), 'name'));

        $response = $this->getAuthenticated('student/subjects');
        $response->assertSee('1 duplicate was skipped');
    }

    public function testTemplateImportRejectsUnlistedCourseIdsAndInvalidProgram(): void
    {
        $student = $this->createUser('ched-invalid-import', 'student');
        $this->authenticateAs($student);

        $this->postAuthenticated('student/subjects/templates/import', [
            'program_id' => 'ba-communication',
            'course_ids' => ['y1s1-english', 'tampered-course'],
        ])->assertRedirect();
        $subjects = new App\Models\SubjectModel();
        $this->assertSame(['English (GE)'], array_column($subjects->findAllForOwner((int) $student->id), 'name'));

        $this->postAuthenticated('student/subjects/templates/import', [
            'program_id' => 'not-a-program',
            'course_ids' => ['y1s1-english'],
        ])->assertRedirect();
        $this->assertSame(['English (GE)'], array_column($subjects->findAllForOwner((int) $student->id), 'name'));
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

        $templateResponse = $this->getAuthenticated('student/subjects/templates');
        $this->assertSame(403, $templateResponse->response()->getStatusCode());

        $shsImportResponse = $this->postAuthenticated('student/subjects/shs/import', [
            'subject_ids' => ['general-science'],
        ]);
        $this->assertSame(403, $shsImportResponse->response()->getStatusCode());

        $importResponse = $this->postAuthenticated('student/subjects/templates/import', [
            'program_id' => 'ba-communication',
            'course_ids' => ['y1s1-english'],
        ]);
        $this->assertSame(403, $importResponse->response()->getStatusCode());
    }

    public function testSignedOutUserCannotUseStudentSubjectRoutes(): void
    {
        $response = $this->get('student/subjects');

        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
        $templateResponse = $this->get('student/subjects/templates');
        $this->assertContains($templateResponse->response()->getStatusCode(), [302, 403]);
        $shsImportResponse = $this->post('student/subjects/shs/import', [
            csrf_token() => csrf_hash(),
            'subject_ids' => ['general-science'],
        ]);
        $this->assertContains($shsImportResponse->response()->getStatusCode(), [302, 403]);
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

    /** @param array<string, mixed> $data */
    private function postAuthenticated(string $path, array $data = []): TestResponse
    {
        $data[csrf_token()] = csrf_hash();
        $this->withSession($_SESSION);
        $response = $this->post($path, $data);
        $this->withSession($_SESSION);

        return $response;
    }
}
