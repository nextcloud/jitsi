<?php

declare(strict_types=1);

namespace OCA\jitsi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IConfig;
use OCP\IUserSession;

class UserController extends Controller
{
    /**
     * @var IUserSession
     */
    private $userSession;

    /**
     * @var IConfig
     */
    private $config;

    /**
     * @var IURLGenerator
     */
    private $urlGenerator;

    public function __construct(
        string $AppName,
        IRequest $request,
        IUserSession $userSession,
        IConfig $config,
        IURLGenerator $urlGenerator
    ) {
        parent::__construct($AppName, $request);
        $this->userSession = $userSession;
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @NoAdminRequired
     * @PublicPage
     */
    public function get(): DataResponse
    {
        $user = $this->userSession->getUser();

        if ($user === null) {
            $userData = null;
        } else {
            $userData = [
                'displayName' => $user->getDisplayName(),
                'avatarURL' => $this->generateAvatarUrl($user->getUID()),
            ];
        }

        return new DataResponse(
            [
                'user' => $userData,
            ]
        );
    }

    private function generateAvatarUrl(string $uid): string
    {
        return $this->urlGenerator->linkToRouteAbsolute('core.avatar.getAvatar', [
            'userId' => $uid,
            'size' => 256,
            'v' => $this->config->getUserValue($uid, 'avatar', 'version', 0)
        ]);
    }
}
