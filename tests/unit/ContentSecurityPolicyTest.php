<?php

use CodeIgniter\HTTP\ContentSecurityPolicy;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use Config\ContentSecurityPolicy as ContentSecurityPolicyConfig;

/**
 * @internal
 */
final class ContentSecurityPolicyTest extends CIUnitTestCase
{
    public function testFinalizeReplacesInlineNoncePlaceholdersAndBuildsPolicy(): void
    {
        $app = config(App::class);
        $originalCspEnabled = $app->CSPEnabled;
        $app->CSPEnabled = true;

        try {
            $response = service('response');
            $response->setBody('<style {csp-style-nonce}>body{color:#000}</style><script {csp-script-nonce}>void 0;</script>');

            $csp = new ContentSecurityPolicy(config(ContentSecurityPolicyConfig::class));
            $csp->finalize($response);

            $body = (string) $response->getBody();
            $policy = $response->getHeaderLine('Content-Security-Policy');

            self::assertSame(1, preg_match('/<style nonce="([^"]+)">/', $body, $styleMatches));
            self::assertSame(1, preg_match('/<script nonce="([^"]+)">/', $body, $scriptMatches));
            self::assertStringContainsString("'nonce-{$styleMatches[1]}'", $policy);
            self::assertStringContainsString("'nonce-{$scriptMatches[1]}'", $policy);
            self::assertStringContainsString("script-src-attr 'none'", $policy);
            self::assertStringContainsString("frame-ancestors 'none'", $policy);
        } finally {
            $app->CSPEnabled = $originalCspEnabled;
        }
    }
}
