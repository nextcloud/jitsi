<?php

namespace OCA\jitsi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IUserSession;

class UserController extends Controller
{
    /**
     * @var IUserSession
     */
    private $userSession;

    public function __construct(
        string $AppName,
        IRequest $request,
        IUserSession $userSession
    ) {
        parent::__construct($AppName, $request);
        $this->userSession = $userSession;
    }

    /**
     * @NoAdminRequired
     * @PublicPage
     */
    public function get(): DataResponse
    {
        if ($this->userSession->isLoggedIn() === false) {
            $userData = null;
        } else {
            $user = $this->userSession->getUser();
            $userData = [
                'displayName' => $user->getDisplayName(),
            ];
        }

        return new DataResponse(
            [
                'user' => $userData,
            ]
        );
    }
}
