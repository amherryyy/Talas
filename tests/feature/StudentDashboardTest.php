<?php

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
final class StudentDashboardTest extends CIUnitTestCase
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

    public function testStudentWorkspaceShowsFixedSuggestionsWithSourceLinks(): void
    {
        $student = $this->createUser('suggestions-student', 'student');
        $this->authenticateAs($student);

        $response = $this->getAuthenticated('student');
        $response->assertOK();
        $response->assertSee('Your Talas workspace');
        $response->assertSee('Study ideas');
        $response->assertSee('Revisit a topic later');
        $response->assertSee('Try recalling before rereading');
        $response->assertSee('Explain why or how');
        $response->assertSee('Read an example, then try one');
        $response->assertSee('https://ies.ed.gov/ncee/wwc/practiceguide/1');
        $response->assertSee('rel="noopener noreferrer"');
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
