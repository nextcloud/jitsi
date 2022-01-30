<?php

declare(strict_types=1);

namespace OCA\jitsi\Controller;

use Browser;
use OCA\jitsi\Config\Config;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;

abstract class AbstractController extends Controller
{
    /**
     * @var IUserSession
     */
    protected $userSession;

    /**
     * @var Config
     */
    protected $appConfig;

    public function __construct(
        string $AppName,
        IRequest $request,
        IUserSession $userSession,
        Config $appConfig
    ) {
        parent::__construct($AppName, $request);
        $this->userSession = $userSession;
        $this->appConfig = $appConfig;
    }

    protected function checkBrowser(): ?TemplateResponse
    {
        $browserInfo = $this->gatherBrowserInfo();

        if ($browserInfo['supported']) {
            return null;
        }

        $loggedIn = $this->userSession->isLoggedIn();
        $renderAs = $loggedIn ? 'user' : 'public';

        return new TemplateResponse(
            'jitsi',
            'outdated_browser',
            ['browserName' => $browserInfo['name'],],
            $renderAs
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function gatherBrowserInfo(): array
    {
        $browser = new Browser();
        $browserName = $browser->getBrowser();
        $browserVersion = $browser->getVersion();

        return [
            'name'      => sprintf(
                '%s %s',
                $browserName,
                $browserVersion
            ),
            'supported' => !(
                $browserName === Browser::BROWSER_IE ||
                ($browserName === Browser::BROWSER_EDGE && $browserVersion < 79) ||
                ($browserName === Browser::BROWSER_SAFARI && $browserVersion < 10)
            )
        ];
    }
}
