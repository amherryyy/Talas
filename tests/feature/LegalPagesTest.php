<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class LegalPagesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $namespace = null;

    public function testLegalPagesArePublicAndClearlyMarkedForReview(): void
    {
        foreach ([
            'terms' => ['Terms and conditions', 'Availability and changes'],
            'privacy' => ['Privacy notice', 'Storage and retention'],
            'cookies' => ['Cookies and browser storage', 'Browser storage'],
        ] as $path => [$title, $section]) {
            $response = $this->get($path);

            $response->assertOK();
            $response->assertSee($title);
            $response->assertSee($section);
            $response->assertSee('Preliminary notice');
            $response->assertSee('Review pending');
            $response->assertSee('href="http://example.com/index.php/privacy"');
            $response->assertSee('href="http://example.com/index.php/terms"');
            $response->assertSee('href="http://example.com/index.php/cookies"');
        }
    }
}
