<?php

use App\Services\LanguageReturnPath;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class LanguageReturnPathTest extends CIUnitTestCase
{
    public function testQueryStyleInfinityFreeRouteIsNotDuplicated(): void
    {
        $this->assertSame(
            'student?s',
            LanguageReturnPath::fromServerUri('index.php', '/index.php?/student?s', 'index.php?'),
        );
    }

    public function testNormalPathKeepsOnlyItsRealQueryString(): void
    {
        $this->assertSame(
            'student/subjects?program=bsis',
            LanguageReturnPath::fromServerUri('/student/subjects', 'program=bsis', 'index.php'),
        );
    }

    public function testRootIndexDoesNotBecomeReturnRoute(): void
    {
        $this->assertSame('', LanguageReturnPath::fromServerUri('/index.php', '', 'index.php'));
    }

    public function testRepeatedLegacyIndexRouteIsCollapsed(): void
    {
        $this->assertSame(
            'student?s',
            LanguageReturnPath::fromServerUri('index.php', '/index.php?/index.php?/student?s', 'index.php?'),
        );
    }
}
