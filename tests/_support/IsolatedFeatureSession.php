<?php

namespace Tests\Support;

use PHPUnit\Framework\Attributes\Before;
use CodeIgniter\Shield\Config\Services as ShieldServices;

/** Prevents PHP session state from leaking between feature tests. */
trait IsolatedFeatureSession
{
    #[Before]
    protected function resetFeatureRuntimeState(): void
    {
        ShieldServices::resetSingle('auth');
        $_SESSION = [];
        service('renderer')->resetData();
    }
}
