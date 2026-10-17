<?php

use App\Services\LocalAdminBootstrapService;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Auth;
use Config\Database;

/**
 * @internal
 */
final class LocalAdminBootstrapTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = null;

    protected function setUp(): void
    {
        $database = config(Database::class);
        $this->assertSame('SQLite3', $database->tests['DBDriver']);
        $this->assertSame(':memory:', $database->tests['database']);
        parent::setUp();
    }

    public function testSetupPolicyAllowsOnlyDevelopmentAndLoopbackDatabase(): void
    {
        $this->assertTrue(LocalAdminBootstrapService::isAllowedEnvironment('development', 'localhost'));
        $this->assertTrue(LocalAdminBootstrapService::isAllowedEnvironment('development', '127.0.0.1'));
        $this->assertTrue(LocalAdminBootstrapService::isAllowedEnvironment('development', '::1'));
        $this->assertFalse(LocalAdminBootstrapService::isAllowedEnvironment('production', 'localhost'));
        $this->assertFalse(LocalAdminBootstrapService::isAllowedEnvironment('testing', '127.0.0.1'));
        $this->assertFalse(LocalAdminBootstrapService::isAllowedEnvironment('development', 'db.example.com'));
        $this->assertFalse(LocalAdminBootstrapService::isAllowedEnvironment('development', ''));
    }

    public function testCreatesOnlyAnAdminAccountWithGeneratedPassword(): void
    {
        $bootstrap = new LocalAdminBootstrapService();
        $account = $bootstrap->create('local-admin-demo', 'local-admin-demo@example.test');

        $this->assertNotNull($account);
        $this->assertTrue($bootstrap->adminExists());
        $this->assertSame(24, strlen($account['password']));
        $this->assertGreaterThanOrEqual(config(Auth::class)->minimumPasswordLength, strlen($account['password']));
        $this->assertLessThanOrEqual(72, strlen($account['password']));
        $this->assertMatchesRegularExpression('/\A[A-Za-z0-9_-]{24}\z/', $account['password']);

        $user = (new UserModel())->findById($account['user_id']);
        $this->assertNotNull($user);
        $this->assertTrue($user->inGroup('admin'));
        $this->assertFalse($user->inGroup('student'));
        $identity = db_connect()->table(config(Auth::class)->tables['identities'])
            ->select('secret, secret2')
            ->where('user_id', $account['user_id'])
            ->where('type', 'email_password')
            ->get()->getRowArray();
        $this->assertIsArray($identity);
        $this->assertNotSame($account['password'], $identity['secret']);
        $this->assertTrue(password_verify($account['password'], $identity['secret2']));
    }

    public function testRefusesToCreateASecondAdmin(): void
    {
        $users = new UserModel();
        $id = $users->insert(new User([
            'username' => 'existing-local-admin',
            'email' => 'existing-local-admin@example.test',
            'password' => 'Synthetic-test-password-123!',
        ]), true);
        $users->findById((int) $id)->addGroup('admin');

        $bootstrap = new LocalAdminBootstrapService();
        $this->assertTrue($bootstrap->adminExists());
        $this->assertNull($bootstrap->create('second-local-admin', 'second-local-admin@example.test'));
        $this->assertSame(1, db_connect()->table(config(Auth::class)->tables['groups_users'])->where('group', 'admin')->countAllResults());
    }
}
