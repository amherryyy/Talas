<?php

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Tests\Support\IsolatedFeatureSession;

/**
 * @internal
 */
final class AuthenticationFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;
    use IsolatedFeatureSession;

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
        $response = $this->postWithCsrf('register', [
            'username'         => 'authregistrationstudent',
            'email'            => 'authregistrationstudent@example.test',
            'password'         => 'K9!vP3#rT6@x',
            'password_confirm' => 'K9!vP3#rT6@x',
            'groups'           => ['admin'],
        ]);
        $response->assertRedirectTo(route_to('auth-action-show'));

        $user = (new UserModel())->where('username', 'authregistrationstudent')->first();

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->inGroup('student'));
        $this->assertFalse($user->inGroup('admin'));
        $this->assertFalse($user->isActivated());
        $this->assertNotNull(model(UserIdentityModel::class)->getIdentityByType(
            $user,
            Session::ID_TYPE_EMAIL_ACTIVATE,
        ));
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

    public function testValidRegistrationCodeActivatesPendingStudent(): void
    {
        $this->postWithCsrf('register', [
            'username'         => 'authverifiedstudent',
            'email'            => 'authverifiedstudent@example.test',
            'password'         => 'K9!vP3#rT6@x',
            'password_confirm' => 'K9!vP3#rT6@x',
        ])->assertRedirectTo(route_to('auth-action-show'));

        $users = new UserModel();
        $user = $users->where('username', 'authverifiedstudent')->first();
        $this->assertInstanceOf(User::class, $user);

        $identity = model(UserIdentityModel::class)->getIdentityByType(
            $user,
            Session::ID_TYPE_EMAIL_ACTIVATE,
        );

        $this->assertNotNull($identity);
        $this->assertMatchesRegularExpression('/\A[0-9]{6}\z/', (string) $identity->secret);
        $this->assertFalse($user->isActivated());

        $this->postWithCsrf('auth/a/verify', ['token' => $identity->secret])
            ->assertRedirect();

        $verifiedUser = $users->findById((int) $user->id);
        $this->assertTrue($verifiedUser->isActivated());
        $this->assertTrue(auth()->loggedIn());
        auth()->logout();
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
        $user->activate();
        $user = $model->findById((int) $userId);
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
