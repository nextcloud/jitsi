<?php

namespace OCA\jitsi\Controller;

use OCA\jitsi\Config\Config;
use OCA\jitsi\Db\RoomMapper;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\FeaturePolicy;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;

class PageController extends AbstractController
{
    /**
     * @var RoomMapper
     */
    private $roomMapper;

    public function __construct(
        string $AppName,
        IRequest $request,
        RoomMapper $roomMapper,
        IUserSession $userSession,
        Config $appConfig
    ) {
        parent::__construct($AppName, $request, $userSession, $appConfig);
        $this->roomMapper = $roomMapper;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): TemplateResponse
    {
        if (($checkBrowserResult = $this->checkBrowser()) !== null) {
            return $checkBrowserResult;
        }

        if (($checkConfiguredResult = $this->checkConfigured()) !== null) {
            return $checkConfiguredResult;
        }

        return new TemplateResponse('jitsi', 'index');
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function room(string $publicId): TemplateResponse
    {
        if (($checkBrowserResult = $this->checkBrowser()) !== null) {
            return $checkBrowserResult;
        }

        if (($checkConfiguredResult = $this->checkConfigured()) !== null) {
            return $checkConfiguredResult;
        }

        $loggedIn = $this->userSession->isLoggedIn();
        $renderAs = $loggedIn ? 'user' : 'public';

        $response = new TemplateResponse(
            'jitsi',
            'room',
            [
                'loggedIn'                         => $loggedIn,
                'serverUrl'                        => $this->appConfig->jitsiServerUrl(),
                'helpLink'                         => $this->appConfig->helpLink(),
                'display_join_using_the_jitsi_app' => $this->appConfig->displayJoinUsingTheJitsiApp(),
            ],
            $renderAs
        );

        $this->setPolicies($response);
        return $response;
    }

    private function setPolicies(Response $response): void
    {
        $serverUrl = $this->appConfig->jitsiServerUrl();
        $serverHost = $this->determineJitsiHost();

        if ($serverUrl === null || $serverHost === null) {
            return;
        }

        $csp = new ContentSecurityPolicy();
        $csp->addAllowedFrameDomain($serverHost);
        $response->setContentSecurityPolicy($csp);

        $fp = new FeaturePolicy();
        $fp->addAllowedCameraDomain('https://nextcloud.local');
        $fp->addAllowedCameraDomain('https://' . $_SERVER['HTTP_HOST']);
        $fp->addAllowedCameraDomain($serverUrl);
        $fp->addAllowedMicrophoneDomain('https://nextcloud.local');
        $fp->addAllowedMicrophoneDomain('https://' . $_SERVER['HTTP_HOST']);
        $fp->addAllowedMicrophoneDomain($serverUrl);
        $response->setFeaturePolicy($fp);
    }

    private function determineJitsiHost(): ?string
    {
        $serverUrl = $this->appConfig->jitsiServerUrl();

        if ($serverUrl === null) {
            return null;
        }

        $urlParts = parse_url($serverUrl);
        // @phpstan-ignore-next-line
        return $urlParts['host'];
    }
}
