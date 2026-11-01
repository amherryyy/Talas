<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;

/**
 * @internal
 */
final class AboutPageTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $namespace = null;

    protected function setUp(): void
    {
        $database = config(Database::class);
        $this->assertSame('SQLite3', $database->tests['DBDriver']);
        $this->assertSame(':memory:', $database->tests['database']);
        parent::setUp();
    }

    public function testAboutPageExplainsCoreFlowAndShowsProjectContactStatus(): void
    {
        $response = $this->get('about');

        $response->assertOK();
        $response->assertSee('How to use Duno');
        $response->assertSee('Why it works this way');
        $response->assertSee('Frequently asked questions');
        $response->assertSee('Reach out, report, connect.');
        $response->assertSee('Report a problem');
        $response->assertSee('Developer social profiles');
        $response->assertSee('https://github.com/amherryyy/Talas/issues/new');
        $response->assertSee('href="mailto:mhracads@gmail.com"');
        $this->assertStringContainsString('aria-label="Email Duno at mhracads@gmail.com"><span>duno@gmail.com</span>', $response->response()->getBody());
        $response->assertSee('Connect with the developer');
        $response->assertSee('https://www.linkedin.com/in/amher-bolante-637443387');
        $response->assertSee('https://www.facebook.com/share/1CcC5xkLrn/');
        $response->assertSee('Duno does not generate personal advice');
        $response->assertSee('href="http://example.com/index.php/terms"');
        $response->assertSee('href="http://example.com/index.php/privacy"');
    }
}
