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
use Tests\Support\IsolatedFeatureSession;

/**
 * @internal
 */
final class StudentSubjectsTest extends CIUnitTestCase
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

    public function testStudentCanCreateViewEditAndDeleteOwnSubjectWithEscapedOutput(): void
    {
        $student = $this->createUser('student-crud', 'student');
        $this->authenticateAs($student);

        $newForm = $this->getAuthenticated('student/subjects/new');
        $newForm->assertOK();
        $newFormBody = $newForm->response()->getBody();
        $this->assertStringContainsString('name="csrf_test_name"', $newFormBody);
        $this->assertStringContainsString('class="button secondary back-link-button"', $newFormBody);
        $this->assertStringContainsString('name="course_id"', $newFormBody);

        $course = (new App\Services\NvsuCurriculumCatalog())->find('bscs-game-development')['courses'][0];
        $this->postAuthenticated('student/subjects', [
            'course_id' => $course['id'],
        ])->assertRedirect();

        $subjects = new App\Models\SubjectModel();
        $this->assertCount(1, $subjects->findAllForOwner((int) $student->id));
        $subject = $subjects->findAllForOwner((int) $student->id)[0];

        $show = $this->getAuthenticated('student/subjects/' . $subject['id']);
        $show->assertOK();
        $showBody = $show->response()->getBody();
        $this->assertStringContainsString('data-mood="success"', $showBody);
        $this->assertStringContainsString('duno-toast-mascot', $showBody);
        $show->assertSee($course['title']);

        $nextCourse = (new App\Services\NvsuCurriculumCatalog())->find('bscs-game-development')['courses'][1];
        $this->postAuthenticated('student/subjects/' . $subject['id'], [
            'course_id' => $nextCourse['id'],
        ])->assertRedirect();

        $this->getAuthenticated('student/subjects/' . $subject['id'])
            ->assertSee($nextCourse['title']);

        $this->postAuthenticated('student/subjects/' . $subject['id'] . '/delete')
            ->assertRedirect();

        $this->assertCount(0, $subjects->findAllForOwner((int) $student->id));
    }

    public function testStudentCanCreateAndFilterSubjectsByNvsuProgram(): void
    {
        $student = $this->createUser('nvsu-program-subjects', 'student');
        $this->authenticateAs($student, 'bsit-web-mobile');

        $program = (new App\Services\NvsuCurriculumCatalog())->find('bsit-web-mobile');
        $this->assertNotNull($program);
        $course = $program['courses'][0];

        $this->postAuthenticated('student/subjects', [
            'course_id' => $course['id'],
            'name' => 'Client supplied title is ignored for a catalog course',
        ])->assertRedirect();

        $subjects = new App\Models\SubjectModel();
        $owned = $subjects->findAllForOwner((int) $student->id, $program['id']);
        $this->assertCount(1, $owned);
        $this->assertSame($course['title'], $owned[0]['name']);
        $this->assertSame($program['id'], $owned[0]['program_id']);

        $index = $this->getAuthenticated('student/subjects');
        $index->assertOK();
        $this->assertStringContainsString('class="subject-catalog-list"', $index->response()->getBody());
        $this->assertStringContainsString('class="subject-catalog-index"', $index->response()->getBody());
        $index->assertSee($course['title']);

        $otherCourse = (new App\Services\NvsuCurriculumCatalog())->find('bsis-business-analytics')['courses'][0];
        $this->postAuthenticated('student/subjects', [
            'program_id' => 'bsis-business-analytics', 'course_id' => $otherCourse['id'],
        ])->assertRedirect();

        $filtered = $this->getAuthenticated('student/subjects?program=' . $program['id']);
        $filtered->assertOK();
        $filtered->assertSee($course['title']);
        $filtered->assertDontSee($otherCourse['title']);
    }

    public function testStudentCannotSubmitCourseFromAnotherNvsuProgram(): void
    {
        $student = $this->createUser('nvsu-course-validation', 'student');
        $this->authenticateAs($student, 'bsit-web-mobile');
        $program = (new App\Services\NvsuCurriculumCatalog())->find('bsit-web-mobile');
        $otherProgram = (new App\Services\NvsuCurriculumCatalog())->find('bsis-business-analytics');
        $this->assertNotNull($program);
        $this->assertNotNull($otherProgram);

        $this->postAuthenticated('student/subjects', [
            'program_id' => $program['id'],
            'course_id' => $otherProgram['courses'][0]['id'],
            'name' => 'Tampered course',
        ])->assertRedirect();

        $this->assertSame([], (new App\Models\SubjectModel())->findAllForOwner((int) $student->id));
        $this->getAuthenticated('student/subjects/new')->assertSee('Choose a subject from your program’s NVSU course list.');
    }

    public function testSubjectListExplainsProgramFilterAndSubjectCreationMeaning(): void
    {
        $student = $this->createUser('nvsu-picker-explanation', 'student');
        $this->authenticateAs($student);

        $list = $this->getAuthenticated('student/subjects');
        $list->assertOK();
        $list->assertSee('Your subjects are for');
        $list->assertDontSee('subject-program-filter');

        $form = $this->getAuthenticated('student/subjects/new');
        $form->assertOK();
        $form->assertSee('has an NVSU subject list. Choose the subject you take from that list.');
        $form->assertSee('name="course_id"');
        $form->assertDontSee('name="name"');
    }

    public function testStudentCanTypeSubjectWhenTheirProgramHasNoNvsuList(): void
    {
        $student = $this->createUser('nvsu-program-without-list', 'student');
        $this->authenticateAs($student, null);

        $form = $this->getAuthenticated('student/subjects/new');
        $form->assertOK();
        $form->assertSee('Duno has no subject list for');
        $form->assertSee('name="name"');
        $form->assertDontSee('name="course_id"');

        $this->postAuthenticated('student/subjects', ['name' => '  Community fieldwork  '])->assertRedirect();

        $subjects = new App\Models\SubjectModel();
        $stored = $subjects->findAllForAcademicProgram((int) $student->id, null);
        $this->assertCount(1, $stored);
        $this->assertSame('Community fieldwork', $stored[0]['name']);
        $this->assertNull($stored[0]['program_id']);

        $otherCourse = (new App\Services\NvsuCurriculumCatalog())->find('bscs-game-development')['courses'][0];
        $tampered = $this->postAuthenticated('student/subjects', [
            'name' => 'Tampered course import',
            'course_id' => $otherCourse['id'],
        ]);
        $tampered->assertRedirect();
        $tampered->assertSessionHas('errors');
        $this->assertCount(1, $subjects->findAllForAcademicProgram((int) $student->id, null));
    }

    public function testStudentCatalogUsesItsInlineBackLinkWithoutADuplicateButton(): void
    {
        $student = $this->createUser('student-page-navigation', 'student');
        $this->authenticateAs($student);

        $subjects = $this->getAuthenticated('student/subjects');
        $subjects->assertOK();
        $subjects->assertDontSee('aria-label="Page navigation"');
        $subjects->assertSee('aria-label="Primary student navigation"');
        $subjects->assertSee('data-home-nav');

        $page = $this->getAuthenticated('student/subjects/templates');
        $page->assertOK();

        $body = $page->response()->getBody();
        $this->assertStringNotContainsString('aria-label="Page navigation"', $body);
        $this->assertStringNotContainsString('aria-label="Go back to the previous page"', $body);
        $this->assertStringContainsString('Back to subjects', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/student/subjects"', $body);
        $this->assertStringNotContainsString('aria-label="Go to home"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/student"', $body);
    }

    public function testStudentCanPreviewNvsuCasCurriculaWithSourceAndYear(): void
    {
        $student = $this->createUser('nvsu-curriculum-preview', 'student');
        $this->authenticateAs($student, 'bsis-business-analytics');

        $options = $this->getAuthenticated('student/subjects/templates');
        $options->assertOK();
        $options->assertSee('Business Analytics');
        $options->assertSee('IST1-6');
        $options->assertDontSee('Network Design and Management');

        $preview = $this->getAuthenticated('student/subjects/templates?nvsu=bsit-web-mobile');
        $preview->assertOK();
        $preview->assertSee('Business Analytics');
        $preview->assertSee('Effective S.Y. 2019');
        $preview->assertSee('IST1-6 — Data Mining');
        $preview->assertSee('https://drive.google.com/file/d/16HEb258jDQRyIA9KoShbUDW-wNaau0jT/view');
        $preview->assertSee('Review the NVSU source');
    }

    public function testNvsuCurriculumImportAddsSelectedCoursesAndSkipsDuplicatesForOwner(): void
    {
        $student = $this->createUser('nvsu-curriculum-import', 'student');
        $otherStudent = $this->createUser('nvsu-curriculum-other', 'student');
        $catalog = new App\Services\NvsuCurriculumCatalog();
        $program = $catalog->find('bsis-business-analytics');
        $this->assertNotNull($program);
        $duplicate = $program['courses'][0];
        $selected = $program['courses'][1];

        $subjects = new App\Models\SubjectModel();
        $subjects->insert(['user_id' => $student->id, 'name' => mb_strtoupper($duplicate['title']), 'program_id' => $program['id']], true);
        $subjects->insert(['user_id' => $otherStudent->id, 'name' => 'Private course'], true);

        $this->authenticateAs($student, 'bsis-business-analytics');
        $this->postAuthenticated('student/subjects/nvsu/import', [
            'nvsu_program_id' => $program['id'],
            'course_ids' => [$duplicate['id'], $selected['id']],
        ])->assertRedirect();

        $this->assertSame(
            [mb_strtoupper($duplicate['title']), $selected['title']],
            array_column($subjects->findAllForOwner((int) $student->id), 'name'),
        );
        $this->assertSame(
            [$program['id'], $program['id']],
            array_column($subjects->findAllForOwner((int) $student->id), 'program_id'),
        );
        $this->assertSame(['Private course'], array_column($subjects->findAllForOwner((int) $otherStudent->id), 'name'));
        $this->getAuthenticated('student/subjects')->assertSee('1 duplicate was skipped');
    }

    public function testInvalidSubjectNameIsRejectedByTheServer(): void
    {
        $student = $this->createUser('student-invalid', 'student');
        $this->authenticateAs($student);

        $response = $this->postAuthenticated('student/subjects', [
            'course_id' => 'not-a-course-in-my-program',
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
        $ownedId = $subjects->insert(['user_id' => $owner->id, 'name' => 'Biology lab', 'program_id' => 'bscs-game-development'], true);
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

    public function testNvsuProgramFilterSubmitsAsPostAndShowsOnlyMatchingSubjects(): void
    {
        $student = $this->createUser('nvsu-program-filter', 'student');
        $subjects = new App\Models\SubjectModel();
        $subjects->insert(['user_id' => $student->id, 'name' => 'Systems Analysis', 'program_id' => 'bsis-service-management']);
        $subjects->insert(['user_id' => $student->id, 'name' => 'Cell Biology', 'program_id' => 'bsit-network-design']);
        $this->authenticateAs($student, 'bsis-service-management');

        $page = $this->getAuthenticated('student/subjects');
        $page->assertOK();
        $pageHtml = $page->response()->getBody();
        $this->assertStringContainsString('method="post" action="http://example.com/index.php/student/subjects/search"', $pageHtml);
        $this->assertStringNotContainsString('subject-program-filter', $pageHtml);

        $filtered = $this->postAuthenticated('student/subjects/search', [
            'query' => 'Systems',
        ]);
        $filtered->assertOK();
        $filtered->assertSee('Systems Analysis');
        $filtered->assertDontSee('Cell Biology');
        $this->assertSame('', $filtered->response()->getHeaderLine('Location'));
    }

    public function testStudentOnlySeesTheNvsuSubjectListForTheirAccountProgram(): void
    {
        $student = $this->createUser('ched-preview-student', 'student');
        $this->authenticateAs($student);

        $catalog = $this->getAuthenticated('student/subjects/templates');
        $catalog->assertOK();
        $catalog->assertSee('NVSU subject list');
        $catalog->assertSee('Review the NVSU source');
        $catalog->assertDontSee('Start from a CHED template');
        $catalog->assertDontSee('Philippine Senior High School subjects');

        $preview = $this->getAuthenticated('student/subjects/templates?program=ba-communication');
        $preview->assertOK();
        $preview->assertSee('Game Development');
        $preview->assertDontSee('Bachelor of Arts in Communication');
    }
    public function testStudentCannotImportSubjectsOutsideTheirSelectedNvsuProgram(): void
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
        $this->assertSame(['general mathematics'], array_column($owned, 'name'));
        $this->assertSame(['Private subject'], array_column($subjects->findAllForOwner((int) $otherStudent->id), 'name'));
        $this->getAuthenticated('student/subjects')->assertSee('general mathematics');
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
        $this->assertCount(1, $owned);
        $this->assertSame(['ENGLISH (ge)'], array_column($owned, 'name'));
        $this->assertSame(['Private course'], array_column($subjects->findAllForOwner((int) $otherStudent->id), 'name'));

        $response = $this->getAuthenticated('student/subjects');
        $response->assertSee('ENGLISH (ge)');
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
        $this->assertSame([], array_column($subjects->findAllForOwner((int) $student->id), 'name'));

        $this->postAuthenticated('student/subjects/templates/import', [
            'program_id' => 'not-a-program',
            'course_ids' => ['y1s1-english'],
        ])->assertRedirect();
        $this->assertSame([], array_column($subjects->findAllForOwner((int) $student->id), 'name'));
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
        $createdUser->activate();
        $createdUser = $userModel->findById((int) $userId);

        return $createdUser;
    }

    private function authenticateAs(User $user, ?string $curriculumId = 'bscs-game-development'): void
    {
        $this->seedAccountProgramWhenStudent($user, $curriculumId);
        $this->actingAs($user);
        $this->withSession($_SESSION);
    }

    private function seedAccountProgramWhenStudent(User $user, ?string $curriculumId): void
    {
        if (! $user->inGroup('student') || $user->inGroup('admin')) { return; }
        $program = null;
        foreach ((new App\Services\AcademicProgramCatalog())->all() as $option) {
            if ($option['curriculum_id'] === $curriculumId) { $program = $option; break; }
        }
        $this->assertNotNull($program);
        db_connect()->table('users')->where('id', (int) $user->id)->update([
            'academic_program_id' => $program['id'], 'academic_program_title' => $program['title'], 'academic_program_selected_at' => '2026-11-01 00:00:00',
        ]);
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
