<?php

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/**
 * @internal
 */
final class AuthenticationFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = null;

    protected function setUp(): void
    {
        $database = config(Database::class);
        $this->assertSame('SQLite3', $database->tests['DBDriver']);
        $this->assertSame(':memory:', $database->tests['database']);
        parent::setUp();
    }

    public function testRegistrationAssignsStudentRoleAndIgnoresSubmittedAdminRole(): void
    {
        $this->postWithCsrf('register', [
            'username'         => 'authregistrationstudent',
            'email'            => 'authregistrationstudent@example.test',
            'password'         => 'K9!vP3#rT6@x',
            'password_confirm' => 'K9!vP3#rT6@x',
            'groups'           => ['admin'],
        ])->assertRedirect();

        $user = (new UserModel())->where('username', 'authregistrationstudent')->first();

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->inGroup('student'));
        $this->assertFalse($user->inGroup('admin'));
    }

    public function testStudentCanSignInReachStudentRouteAndSignOut(): void
    {
        $this->createStudent('auth-login-student');

        $this->postWithCsrf('login', [
            'email'    => 'auth-login-student@example.test',
            'password' => 'K9!vP3#rT6@x',
        ])->assertRedirect();

        $this->get('student')->assertOK();

        $this->postWithCsrf('logout')->assertRedirect();

        $response = $this->get('student');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    public function testInvalidCredentialsDoNotGrantStudentAccess(): void
    {
        $this->createStudent('auth-invalid-login-student');

        $this->postWithCsrf('login', [
            'email'    => 'auth-invalid-login-student@example.test',
            'password' => 'incorrect-password',
        ])->assertRedirect();

        $response = $this->get('student');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    private function createStudent(string $username): void
    {
        $model = new UserModel();
        $userId = $model->insert(new User([
            'username' => $username,
            'email'    => $username . '@example.test',
            'password' => 'K9!vP3#rT6@x',
        ]));

        $user = $model->findById((int) $userId);
        $user->addGroup('student');
    }

    /** @param array<string, mixed> $data */
    private function postWithCsrf(string $path, array $data = []): \CodeIgniter\Test\TestResponse
    {
        $data[csrf_token()] = csrf_hash();
        $this->withSession($_SESSION);
        $response = $this->post($path, $data);
        $this->withSession($_SESSION);

        return $response;
    }
}
