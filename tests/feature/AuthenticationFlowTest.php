<?php

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\PasswordResetCodeModel;
use CodeIgniter\I18n\Time;
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
    private string $testSuffix;

    protected function setUp(): void
    {
        $database = config(Database::class);
        $this->assertSame('SQLite3', $database->tests['DBDriver']);
        $this->assertSame(':memory:', $database->tests['database']);
        parent::setUp();
        $this->testSuffix = bin2hex(random_bytes(6));
        \Config\Services::reset('throttler');
        \Config\Services::reset('cache');
        service('cache')->deleteMatching('throttler_*');
    }

    public function testAdminEntryUsesExistingLoginWhileRegistrationRemainsStudentOnly(): void
    {
        $studentLogin = $this->get('login');
        $studentLogin->assertSee('Admin sign in');
        $studentLogin->assertSee('admin-sign-in');
        $adminLogin = $this->get('admin-sign-in');
        $adminLogin->assertOK();
        $adminLogin->assertSee('Send a request');
        $adminLogin->assertSee('Student sign in');
        $registration = $this->get('register');
        $registration->assertSee('Admin sign in');
        $registration->assertSee('admin-sign-in');
        $registration->assertSee('registration-consent-checkbox');
        $this->assertSame('student', config(\Config\AuthGroups::class)->defaultGroup);
    }

    public function testApprovedAdminCanSignInFromAdminEntryAndReachDashboard(): void
    {
        $username = 'approved-admin-' . $this->testSuffix;
        $users = new UserModel();
        $userId = $users->insert(new User([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => 'K9!vP3#rT6@x',
        ]));
        $admin = $users->findById((int) $userId);
        $admin->addGroup('admin');
        $admin->activate();

        $this->get('admin-sign-in')->assertOK();
        $this->postWithCsrf('login', [
            'email' => $username . '@example.test',
            'password' => 'K9!vP3#rT6@x',
        ])->assertRedirect();

        $this->assertTrue(auth()->loggedIn());
        $this->get('admin')->assertOK();
    }

    public function testRegistrationAssignsStudentRoleAndIgnoresSubmittedAdminRole(): void
    {
        $response = $this->postWithCsrf('register', [
            'username'         => 'authregistration' . $this->testSuffix,
            'email'            => 'auth-registration-' . $this->testSuffix . '@example.test',
            'password'         => 'K9!vP3#rT6@x',
            'password_confirm' => 'K9!vP3#rT6@x',
            'terms_privacy_consent' => '1',
            'groups'           => ['admin'],
        ]);
        $response->assertRedirect();

        $user = (new UserModel())->where('username', 'authregistration' . $this->testSuffix)->first();

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->inGroup('student'));
        $this->assertFalse($user->inGroup('admin'));
        $this->assertFalse($user->isActivated());
        $this->assertNotNull(model(UserIdentityModel::class)->getIdentityByType(
            $user,
            Session::ID_TYPE_EMAIL_ACTIVATE,
        ));
    }

    public function testRegistrationRequiresTermsAndPrivacyConsentOnTheServer(): void
    {
        $this->postWithCsrf('register', [
            'username'         => 'authconsentrequired' . $this->testSuffix,
            'email'            => 'auth-consent-required-' . $this->testSuffix . '@example.test',
            'password'         => 'K9!vP3#rT6@x',
            'password_confirm' => 'K9!vP3#rT6@x',
        ])->assertRedirect();

        $user = (new UserModel())->where('username', 'authconsentrequired' . $this->testSuffix)->first();
        $this->assertNull($user);
    }

    public function testUnknownEmailPasswordRecoveryUsesNeutralResponse(): void
    {
        $response = $this->postWithCsrf('forgot-password', [
            'email' => 'not-registered-' . $this->testSuffix . '@example.test',
        ]);

        $response->assertRedirectTo(route_to('password.reset'));
        $page = $this->get('reset-password');
        $page->assertOK();
        $page->assertSee(lang('Registration.resetRequestReceived'));
        $this->assertSame(0, (new PasswordResetCodeModel())->countAllResults());
    }

    public function testValidPasswordResetCodeChangesPasswordAndCannotBeReused(): void
    {
        $user = $this->createStudent('auth-password-reset');
        $code = '004271';
        $now = Time::now();
        $codes = new PasswordResetCodeModel();
        $codes->insert([
            'user_id'         => (int) $user->id,
            'code_hash'       => password_hash($code, PASSWORD_DEFAULT),
            'failed_attempts' => 0,
            'expires_at'      => $now->addMinutes(15)->toDateTimeString(),
            'created_at'      => $now->toDateTimeString(),
        ]);

        $newPassword = 'M4!qR8@vL2#s';
        $data = [
            'email'            => $user->email,
            'code'             => $code,
            'password'         => $newPassword,
            'password_confirm' => $newPassword,
        ];
        $this->postWithCsrf('reset-password', $data)->assertRedirectTo(route_to('login'));

        $users = new UserModel();
        $updatedUser = $users->findById((int) $user->id);
        $this->assertTrue(service('passwords')->verify($newPassword, $updatedUser->getPasswordHash()));
        $this->assertSame(0, $codes->countAllResults());

        $this->postWithCsrf('reset-password', $data)->assertRedirect();
        $updatedUser = $users->findById((int) $user->id);
        $this->assertTrue(service('passwords')->verify($newPassword, $updatedUser->getPasswordHash()));
    }

    public function testFiveIncorrectPasswordResetCodesInvalidateTheCode(): void
    {
        $user = $this->createStudent('auth-password-reset-attempts');
        $now = Time::now();
        $codes = new PasswordResetCodeModel();
        $codeId = $codes->insert([
            'user_id'         => (int) $user->id,
            'code_hash'       => password_hash('004271', PASSWORD_DEFAULT),
            'failed_attempts' => 0,
            'expires_at'      => $now->addMinutes(15)->toDateTimeString(),
            'created_at'      => $now->toDateTimeString(),
        ]);

        for ($attempt = 0; $attempt < PasswordResetCodeModel::MAX_FAILED_ATTEMPTS; $attempt++) {
            $response = $this->postWithCsrf('reset-password', [
                'email'            => $user->email,
                'code'             => '999999',
                'password'         => 'M4!qR8@vL2#s',
                'password_confirm' => 'M4!qR8@vL2#s',
            ]);
            $this->assertSame(302, $response->response()->getStatusCode(), 'Reset attempt returned HTTP ' . $response->response()->getStatusCode());
            $response->assertRedirect();
        }

        $this->assertNull($codes->find($codeId));
    }

    public function testStudentCanSignInReachStudentRouteAndSignOut(): void
    {
        $student = $this->createStudent('auth-login-student');

        $loginResponse = $this->postWithCsrf('login', [
            'email'    => $student->email,
            'password' => 'K9!vP3#rT6@x',
        ]);
        $loginResponse->assertRedirect();

        $this->get('student')->assertRedirectTo(site_url('student/academic-program'));
        $this->get('student/academic-program')->assertOK();

        // CI's testing environment intentionally avoids starting native PHP
        // sessions; Shield regenerates the session ID during logout.
        session_start();
        $this->postWithCsrf('logout')->assertRedirect();
        session_write_close();
        $login = $this->get('login');
        $login->assertOK();
        $login->assertSee('You’ve logged out.');

        $response = $this->get('student');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    public function testValidRegistrationCodeActivatesPendingStudent(): void
    {
        $registrationResponse = $this->postWithCsrf('register', [
            'username'         => 'authverified' . $this->testSuffix,
            'email'            => 'auth-verified-' . $this->testSuffix . '@example.test',
            'password'         => 'K9!vP3#rT6@x',
            'password_confirm' => 'K9!vP3#rT6@x',
            'terms_privacy_consent' => '1',
        ]);
        $registrationResponse->assertRedirect();

        $users = new UserModel();
        $user = $users->where('username', 'authverified' . $this->testSuffix)->first();
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
    }

    public function testInvalidCredentialsDoNotGrantStudentAccess(): void
    {
        $student = $this->createStudent('auth-invalid-login-student');

        $loginResponse = $this->postWithCsrf('login', [
            'email'    => $student->email,
            'password' => 'incorrect-password',
        ]);
        $loginResponse->assertRedirect();

        $response = $this->get('student');
        $this->assertContains($response->response()->getStatusCode(), [302, 403]);
    }

    private function createStudent(string $username): User
    {
        $username .= '-' . $this->testSuffix;
        $model = new UserModel();
        $userId = $model->insert(new User([
            'username' => $username,
            'email'    => $username . '@example.test',
            'password' => 'K9!vP3#rT6@x',
        ]));

        $user = $model->findById((int) $userId);
        $user->addGroup('student');
        $user->activate();

        return $model->findById((int) $userId);
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
