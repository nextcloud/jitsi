<?php

declare(strict_types=1);

namespace OCA\jitsi\Controller;

use Ahc\Jwt\JWT;
use OCA\jitsi\Config\Config;
use OCA\jitsi\Db\Room;
use OCA\jitsi\Db\RoomMapper;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IUserSession;
use Ramsey\Uuid\Uuid;

class RoomController extends AbstractController {
	/**
	 * @var RoomMapper
	 */
	private $roomMapper;

	/**
	 * @var string|null
	 */
	private $userId;

	public function __construct(
		string $AppName,
		IRequest $request,
		RoomMapper $roomMapper,
		?string $UserId,
		IUserSession $userSession,
		Config $appConfig
	) {
		parent::__construct($AppName, $request, $userSession, $appConfig);
		$this->roomMapper = $roomMapper;
		$this->userId = $UserId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		if ($this->userSession->isLoggedIn() === false) {
			return new DataResponse([]);
		}

		$user = $this->userSession->getUser();

		if ($user === null) {
			$rooms = [];
		} else {
			$rooms = $this->roomMapper->findAllByCreator($user);
		}

		return new DataResponse($rooms);
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $name): DataResponse {
		if ($this->userId === null) {
			return new DataResponse([]);
		}

		$room = new Room();
		$room->setName($name);
		$room->setCreatorId($this->userId);

		$uuid = Uuid::uuid4();
		$room->setPublicId($uuid->toString());

		return new DataResponse($this->roomMapper->insert($room));
	}

	/**
	 * @NoAdminRequired
	 */
	public function delete(string $id): DataResponse {
		$room = $this->roomMapper->findOneByPublicId($id);

		if ($room === null) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

        if ($room->getCreatorId() !== $this->userId) {
            return new DataResponse(null, Http::STATUS_FORBIDDEN);
        }

		$this->roomMapper->delete($room);
		return new DataResponse($room);
	}

	/**
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function get(string $publicId): DataResponse {
		$room = $this->roomMapper->findOneByPublicId($publicId);

		if ($room === null) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

		return new DataResponse($room);
	}

	/**
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function token(string $publicId, ?string $displayName): DataResponse {
		$user = $this->userSession->getUser();

		if ($user !== null) {
			$displayName = $user->getDisplayName();
		}

		$room = $this->roomMapper->findOneByPublicId($publicId);

		if ($room === null) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

		$jwtSecret = $this->appConfig->jwtSecret();

		if (empty($jwtSecret)) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

		$context = [
			'user' => [
				'name' => $displayName,
                                'lobby' => $user === null,			
			],
		];

		$jwt = new JWT($jwtSecret, 'HS256');
		$token = $jwt->encode(
			[
				'context' => $context,
				'aud' => $this->appConfig->jwtAudience(),
				'iss' => $this->appConfig->jwtIssuer(),
				'sub' => '*',
				'room' => $room->getPublicId(),
				'exp' => time() + 12 * 60 * 60,
			]
		);

		return new DataResponse(
			[
				'token' => $token,
			]
		);
	}
}
