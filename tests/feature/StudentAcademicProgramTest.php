<?php

use App\Services\AcademicProgramCatalog;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use Config\Database;
use Tests\Support\IsolatedFeatureSession;

/** @internal */
final class StudentAcademicProgramTest extends CIUnitTestCase
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

    public function testStudentMustSelectOneCurrentProgramBeforeWorkspaceAccess(): void
    {
        $student = $this->createStudent('program-gate-student');
        $this->actingAs($student);
        $this->withSession($_SESSION);

        $this->get('student')->assertRedirectTo(site_url('student/academic-program'));
        $form = $this->get('student/academic-program');
        $form->assertOK();
        $form->assertSee('What program are you taking now?');
        $form->assertSee('data-academic-college');
        $form->assertSee('data-academic-program');
        $form->assertSee('Only NVSU Bayombong programs are listed.');
        $form->assertDontSee('Bambang Campus');
        $form->assertSee('I confirm this is my current academic program.');
        $form->assertDontSee('data-auto-start="true"');
        $form->assertDontSee('Primary student navigation');
    }

    public function testCatalogChoiceIsSavedOnceAndReadOnlyInProfile(): void
    {
        $student = $this->createStudent('program-save-student');
        $this->actingAs($student);
        $this->withSession($_SESSION);
        $catalog = new AcademicProgramCatalog();
        $choices = $catalog->forCampus('Bayombong Campus');
        $choice = $choices[0];
        $otherCollegeChoice = array_values(array_filter(
            $choices,
            static fn (array $candidate): bool => $candidate['college'] !== $choice['college'],
        ))[0];

        $invalid = $this->postWithCsrf('student/academic-program', [
            'academic_program_id' => 'forged-program', 'confirm_current_program' => '1',
        ]);
        $invalid->assertRedirectTo(site_url('student/academic-program'));
        $this->assertNull(db_connect()->table('users')->select('academic_program_selected_at')->where('id', (int) $student->id)->get()->getRow('academic_program_selected_at'));

        $mismatchedCollege = $this->postWithCsrf('student/academic-program', [
            'college' => $otherCollegeChoice['college'],
            'academic_program_id' => $choices[0]['id'],
            'confirm_current_program' => '1',
        ]);
        $mismatchedCollege->assertRedirectTo(site_url('student/academic-program'));
        $this->assertNull(db_connect()->table('users')->select('academic_program_selected_at')->where('id', (int) $student->id)->get()->getRow('academic_program_selected_at'));

        $saved = $this->postWithCsrf('student/academic-program', [
            'college' => $choice['college'],
            'academic_program_id' => $choice['id'], 'confirm_current_program' => '1',
        ]);
        $saved->assertRedirectTo(site_url('student'));
        $profile = db_connect()->table('users')->select('academic_program_id, academic_program_title, academic_program_selected_at')
            ->where('id', (int) $student->id)->get()->getRowArray();
        $this->assertSame($choice['id'], $profile['academic_program_id']);
        $this->assertSame($choice['title'], $profile['academic_program_title']);
        $this->assertNotNull($profile['academic_program_selected_at']);

        $this->get('student/academic-program')->assertRedirectTo(site_url('student'));
        $this->postWithCsrf('student/academic-program', [
            'college' => $otherCollegeChoice['college'],
            'academic_program_id' => $otherCollegeChoice['id'], 'confirm_current_program' => '1',
        ])->assertRedirectTo(site_url('student'));
        $this->assertSame($choice['id'], db_connect()->table('users')->select('academic_program_id')->where('id', (int) $student->id)->get()->getRow('academic_program_id'));

        $profilePage = $this->get('student/profile');
        $profilePage->assertOK();
        $profilePage->assertSee($choice['title']);
        $profilePage->assertSee('class="profile-readonly-shell profile-readonly-shell-program" role="group"');
        $profilePage->assertDontSee('name="academic_program_id"');
        $profilePage->assertSee('id="duno-language"');
        $profilePage->assertSee('name="return_to" value="student/profile"');
        $profilePage->assertSee('Preferences');
        $this->postWithCsrf('student/profile', [
            'username' => 'program-save-student', 'academic_program_id' => (new AcademicProgramCatalog())->all()[1]['id'],
        ])->assertRedirect();
        $this->assertSame($choice['id'], db_connect()->table('users')->select('academic_program_id')->where('id', (int) $student->id)->get()->getRow('academic_program_id'));
    }

    private function createStudent(string $username): User
    {
        $model = new UserModel();
        $user = new User(['username' => $username, 'email' => $username . '@example.test', 'password' => 'Synthetic-test-password-123!']);
        $id = $model->insert($user);
        $this->assertNotFalse($id);
        $created = $model->findById((int) $id);
        $created->addGroup('student');
        $created->activate();

        return $model->findById((int) $id);
    }

    /** @param array<string, mixed> $data */
    private function postWithCsrf(string $path, array $data = []): TestResponse
    {
        $data[csrf_token()] = csrf_hash();
        $this->withSession($_SESSION);
        $response = $this->post($path, $data);
        $this->withSession($_SESSION);

        return $response;
    }
}
