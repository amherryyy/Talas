<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use Config\Database;
use Tests\Support\IsolatedFeatureSession;

/**
 * @internal
 */
final class HomeTest extends CIUnitTestCase
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

    public function testPublicStartPageIntroducesDunoAndLinksToAccountActions(): void
    {
        $response = $this->get('/');

        $response->assertOK();
        $response->assertSee('duno.');
        $response->assertSee('Your Little Study Buddy.');
        $response->assertSee('Duno Focus');
        $response->assertSee('Duno Plan');
        $response->assertSee('Duno Timer');
        $response->assertSee('Aralytics');
        $response->assertSee('Duno Guide');
        $response->assertSee('A clearer way to begin studying.');
        $response->assertSee('Meet the tools');
        $response->assertSee('tool-card-focus');
        $response->assertSee('assets/images/duno-mascot.png');
        $response->assertSee('assets/images/duno-mascot-animation.webp');
        $response->assertSee('data-duno-mascot');
        $response->assertSee('data-duno-interactive');
        $body = $response->response()->getBody();
        $this->assertStringContainsString('href="http://example.com/index.php/register"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/login"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/terms"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/privacy"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/cookies"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/about"', $body);
        $this->assertStringContainsString('data-footer-disclosure', $body);
        $this->assertStringNotContainsString('data-app-tour', $body);
        $response->assertDontSee('Welcome to CodeIgniter');
    }

    public function testPublicAndAuthenticationPagesUseExistingHomeNavigationWithoutDuplicateControls(): void
    {
        foreach (['/', 'login', 'register'] as $path) {
            $response = $this->get($path);
            $response->assertOK();

            $body = $response->response()->getBody();
            $this->assertStringNotContainsString('aria-label="Page navigation"', $body);
            $this->assertStringNotContainsString('aria-label="Go back to the previous page"', $body);
            $this->assertStringNotContainsString('aria-label="Go to home"', $body);
            $this->assertStringContainsString('aria-label="duno.', $body);
        }
    }

    public function testStudentHomePresentsFocusAsTheFirstMoveAndKeepsOtherToolsCompact(): void
    {
        $student = $this->createUser('home-layout-student');
        $this->actingAs($student);
        $this->withSession($_SESSION);

        $response = $this->get('/');
        $response->assertOK();
        $response->assertSee('What are we studying today?');
        $response->assertSee('href="#tools">Choose a tool');
        $response->assertSee('class="tool-card tool-card-focus lavender"');
        $response->assertSee('NEXT TOPIC');
        $response->assertSee('STUDY SCHEDULE');
        $response->assertSee('STUDY SESSION');
        $response->assertSee('STUDY HISTORY');
    }

    private function createUser(string $username): User
    {
        $model = new UserModel();
        $user = new User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'Synthetic-test-password-123!',
        ]);
        $userId = $model->insert($user);
        $created = $model->findById((int) $userId);
        $created->addGroup('student');
        $created->activate();

        return $model->findById((int) $userId);
    }
}
