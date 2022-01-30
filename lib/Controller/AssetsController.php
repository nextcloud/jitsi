<?php

declare(strict_types=1);

namespace OCA\jitsi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\StreamResponse;

class AssetsController extends Controller
{
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function soundsTest(): StreamResponse
    {
        return (new StreamResponse(__DIR__ . '/../../sounds/coin.wav'))
            ->cacheFor(24 * 60 * 60);
    }
}
