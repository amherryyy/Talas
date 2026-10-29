<?php

use App\Services\InfinityFreeRouteMarker;
use PHPUnit\Framework\TestCase;

final class InfinityFreeRouteMarkerTest extends TestCase
{
    public function testStripsTheTerminalHostMarkerAfterAnAppQueryParameter(): void
    {
        $uri = '/index.php?/student/plan?date=2026-10-29&i=1';

        $this->assertSame(
            '/index.php?/student/plan?date=2026-10-29',
            InfinityFreeRouteMarker::stripTerminalMarker($uri, '/index.php'),
        );
    }

    public function testLeavesPathStyleUrlsAndOtherFrontControllersUntouched(): void
    {
        $pathStyle = '/student/plan?date=2026-10-29&i=1';
        $otherScript = '/index.php?/student/plan?date=2026-10-29&i=1';

        $this->assertSame($pathStyle, InfinityFreeRouteMarker::stripTerminalMarker($pathStyle, '/index.php'));
        $this->assertSame($otherScript, InfinityFreeRouteMarker::stripTerminalMarker($otherScript, '/other.php'));
    }
}
