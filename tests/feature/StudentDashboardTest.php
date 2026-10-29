<?php

use App\Models\CheckInModel;
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
        $response->assertSee('Duno Focus');
        $response->assertSee('Duno Plan');
        $response->assertSee('Duno Guide');
        $response->assertSee('Duno Timer');
        $response->assertSee('Aralytics');
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
        $this->assertStringContainsString('data-app-tour', $studentHtml);
        $this->assertStringContainsString('data-tour-user="' . $student->id . '"', $studentHtml);
        $this->assertStringContainsString('assets/js/onboarding-tour.js', $studentHtml);
        $this->assertStringContainsString('Show app tour', $studentHtml);
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

    public function testPrivateCheckInsDoNotEarnStudyXp(): void
    {
        $student = $this->createUser('check-in-no-xp-student', 'student');
        (new CheckInModel())->createForOwner((int) $student->id, 5, 1, date('Y-m-d H:i:s'));
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('student');
        $response->assertOK();
        $response->assertSee('Level 1');
        $response->assertSee('500 XP');
        $response->assertSee('0 study + 500 streak');
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
