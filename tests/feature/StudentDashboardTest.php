<?php

use App\Models\CheckInModel;
use App\Models\SubjectModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\TestResponse;
use Config\Database;
use Tests\Support\IsolatedFeatureSession;

/**
 * @internal
 */
final class StudentDashboardTest extends CIUnitTestCase
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

    public function testStudentWorkspaceShowsFixedSuggestionsWithSourceLinks(): void
    {
        $student = $this->createUser('suggestions-student', 'student');
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('student');
        $response->assertOK();
        $response->assertSee('Your study space');
        $response->assertSee('duno.');
        $response->assertSee('Your Little Study Buddy.');
        $response->assertSee('assets/images/duno-mascot.png');
        $response->assertSee('assets/images/duno-mascot-animation.webp');
        $response->assertSee('data-duno-mascot');
        $response->assertSee('data-duno-interactive');
        $response->assertSee('Duno Focus');
        $response->assertSee('Duno Plan');
        $response->assertSee('Duno Guide');
        $response->assertSee('Duno Timer');
        $response->assertSee('Aralytics');
        $response->assertSee('1,000 XP per saved study minute; +5,000 XP for your first app visit each day.');
        $response->assertSee('Revisit a topic later');
        $response->assertSee('Try recalling before rereading');
        $response->assertSee('Explain why or how');
        $response->assertSee('Read an example, then try one');
        $response->assertSee('https://ies.ed.gov/ncee/wwc/practiceguide/1');
        $response->assertSee('rel="noopener noreferrer"');
        $studentHtml = $response->response()->getBody();
        $this->assertStringContainsString('aria-label="Primary student navigation"', $studentHtml);
        $this->assertStringContainsString('aria-current="page"', $studentHtml);
        $this->assertStringContainsString('index.php/student/aralytics', $studentHtml);
        $this->assertStringContainsString('href="http://example.com/index.php/student/subjects">Manage subjects', $studentHtml);
        $this->assertStringNotContainsString('>Browse topics', $studentHtml);
        $this->assertStringContainsString('>Study tools</h2>', $studentHtml);
        $this->assertStringContainsString('data-app-tour', $studentHtml);
        $this->assertStringContainsString('data-tour-user="' . $student->id . '"', $studentHtml);
        $this->assertStringContainsString('assets/js/onboarding-tour.js', $studentHtml);
        $this->assertStringContainsString('Take the guided tour', $studentHtml);
        $this->assertStringContainsString('New to Duno?', $studentHtml);
        $this->assertStringContainsString('href="http://example.com/index.php/student#app-tour"', $studentHtml);
        preg_match('/<details class="student-menu".*?<\/details>/s', $studentHtml, $studentMenuMatch);
        $studentMenuHtml = $studentMenuMatch[0] ?? '';
        $this->assertNotSame('', $studentMenuHtml);
        $this->assertStringContainsString('student/subjects', $studentMenuHtml);
        $this->assertStringContainsString('student/topics', $studentMenuHtml);
        $this->assertStringContainsString('student/check-ins', $studentMenuHtml);
        $this->assertStringContainsString('student/profile', $studentMenuHtml);
        $this->assertStringContainsString('data-guide-nav', $studentMenuHtml);
        $this->assertStringNotContainsString('data-home-nav', $studentMenuHtml);
        $this->assertStringNotContainsString('Show app tour', $studentMenuHtml);
        $this->assertStringNotContainsString('duno-language', $studentMenuHtml);
    }

    public function testStudentLandingOffersTheAccountScopedOnboardingTour(): void
    {
        $student = $this->createUser('onboarding-home-student', 'student');
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('/');
        $response->assertOK();
        $studentHtml = $response->response()->getBody();

        $this->assertStringContainsString('data-auto-start="true"', $studentHtml);
        $this->assertStringContainsString('data-tour-user="' . $student->id . '"', $studentHtml);
        $this->assertStringContainsString('data-tour-target="focus"', $studentHtml);
        $this->assertStringContainsString('href="http://example.com/index.php/student#app-tour"', $studentHtml);
    }

    public function testDashboardShowsOnlyCurrentProgramSubjectsAndTheirTopicActions(): void
    {
        $student = $this->createUser('dashboard-subject-owner', 'student');
        $other = $this->createUser('dashboard-subject-other', 'student');
        $subjects = new SubjectModel();
        $currentId = $subjects->insert([
            'user_id' => $student->id,
            'name' => 'Algorithms',
            'program_id' => 'bscs-game-development',
        ], true);
        $subjects->insert([
            'user_id' => $student->id,
            'name' => '.',
            'program_id' => 'bscs-game-development',
        ], true);
        $subjects->insert([
            'user_id' => $student->id,
            'name' => 'Out of program',
            'program_id' => 'bsit-web-mobile',
        ], true);
        $subjects->insert([
            'user_id' => $other->id,
            'name' => 'Private subject',
            'program_id' => 'bscs-game-development',
        ], true);
        $this->assertNotFalse($currentId);
        (new App\Models\TopicModel())->insertForSubjectOwner((int) $currentId, (int) $student->id, [
            'title' => 'Big O notation',
            'due_date' => null,
            'importance' => 'medium',
            'difficulty' => 'medium',
            'progress' => 'not_started',
        ]);
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('student');
        $response->assertOK();
        $this->assertStringContainsString('id="dashboard-subjects-heading">Subjects</h2>', $response->response()->getBody());
        $response->assertSee('Algorithms');
        $response->assertSee('Untitled subject');
        $response->assertDontSee('>.</a>');
        $response->assertSee('Big O notation');
        $response->assertSee('1 topic');
        $response->assertSee('Manage topics');
        $response->assertDontSee('Out of program');
        $response->assertDontSee('Private subject');
    }

    public function testGuideOutcomeChoicesAreLimitedToTheStudentsLinkedComputingSubjects(): void
    {
        $student = $this->createUser('guide-outcome-owner', 'student');
        $other = $this->createUser('guide-outcome-other', 'student');
        $subjects = new SubjectModel();
        $bscsSubjectId = $subjects->insert(['user_id' => $student->id, 'name' => 'My BSCS', 'program_id' => 'bscs-game-development']);
        $subjects->insert(['user_id' => $student->id, 'name' => 'General writing', 'program_id' => 'ba-communication']);
        $subjects->insert(['user_id' => $other->id, 'name' => 'Private BSIT', 'program_id' => 'bsit-web-mobile']);
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('student');
        $response->assertOK();
        $html = $response->response()->getBody();
        $this->assertStringContainsString('value="cs" data-guide-subject-id="' . $bscsSubjectId . '">My BSCS</option>', $html);
        $this->assertStringContainsString('data-guide-picker="subject"', $html);
        $this->assertStringContainsString('data-guide-picker="topic"', $html);
        $this->assertStringNotContainsString('Private BSIT', $html);
        $this->assertStringNotContainsString('<option value="is">General writing</option>', $html);
        $this->assertStringContainsString('CS10', $html);
        $this->assertStringContainsString('CHED CMO No. 25 (2015)', $html);
        $this->assertStringContainsString('Select a subject or topic for tailored guidance.', $html);
    }

    public function testPrivateCheckInsDoNotEarnStudyXp(): void
    {
        $student = $this->createUser('check-in-no-xp-student', 'student');
        (new CheckInModel())->createForOwner((int) $student->id, 5, 1, date('Y-m-d H:i:s'));
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('student');
        $response->assertOK();
        $response->assertSee('Level 1');
        $response->assertSee('5,000 XP');
        $response->assertSee('0 study + 5,000 streak');
    }

    public function testSuggestionsDoNotAppearOnAdminWorkspaceAndStudentRouteDeniesAdmin(): void
    {
        $admin = $this->createUser('suggestions-admin', 'admin');
        $this->authenticateAs($admin);

        $this->getAuthenticated('student')->assertStatus(403);
        $adminResponse = $this->getAuthenticated('admin');
        $adminResponse->assertOK();
        $adminResponse->assertDontSee('Study ideas');
        $adminResponse->assertDontSee('Revisit a topic later');
        $adminHtml = $adminResponse->response()->getBody();
        $this->assertStringNotContainsString('data-app-tour', $adminHtml);
        $this->assertStringContainsString('aria-label="Admin navigation"', $adminHtml);
        $this->assertStringContainsString('index.php/admin', $adminHtml);

        auth()->logout();
        $response = $this->get('student');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
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
        $created->activate();
        $created = $model->findById((int) $userId);

        return $created;
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
}
