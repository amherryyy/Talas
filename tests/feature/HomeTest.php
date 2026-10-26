<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/**
 * @internal
 */
final class HomeTest extends CIUnitTestCase
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
        $response->assertSee('assets/images/duno-mascot.png');
        $body = $response->response()->getBody();
        $this->assertStringContainsString('href="http://example.com/index.php/register"', $body);
        $this->assertStringContainsString('href="http://example.com/index.php/login"', $body);
        $this->assertStringNotContainsString('data-app-tour', $body);
        $response->assertDontSee('Welcome to CodeIgniter');
    }
}
