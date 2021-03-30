<?php

namespace OCA\jitsi\Controller;

use Ahc\Jwt\JWT;
use OCA\jitsi\AppInfo\Application;
use OCA\jitsi\Db\Room;
use OCA\jitsi\Db\RoomMapper;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

use function uniqid;

class RoomController extends AbstractController
{
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
		IConfig $config
	) {
		parent::__construct($AppName, $request, $userSession, $config);
		$this->roomMapper = $roomMapper;
		$this->userId = $UserId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse
	{
		if ($this->userSession->isLoggedIn() === false) {
			return new DataResponse([]);
		}

		$user = $this->userSession->getUser();
		return new DataResponse($this->roomMapper->findAllByCreator($user));
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $name): DataResponse
	{
		$room = new Room();
		$room->setName($name);
		$room->setCreatorId($this->userId);
		$room->setPublicId(uniqid());
		return new DataResponse($this->roomMapper->insert($room));
	}

	/**
	 * @NoAdminRequired
	 */
	public function delete(int $id): DataResponse
	{
		$room = $this->roomMapper->findOneById($id);
		$this->roomMapper->delete($room);
		return new DataResponse($room);
	}

	/**
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function get(string $publicId): DataResponse
	{
		$room = $this->roomMapper->findOneByPublicId($publicId);

		if ($room === null) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}

		return new DataResponse(
			$this->roomMapper->findOneByPublicId($publicId)
		);
	}

	/**
	 * @NoAdminRequired
	 * @PublicPage
	 */
	public function token(string $publicId, ?string $displayName): DataResponse
	{
		if ($this->userSession->isLoggedIn()) {
			$displayName = $this->userSession->getUser()->getDisplayName();
		}

		$jwtSecret = $this->config->getAppValue(
			Application::APP_ID,
			'jwt_secret'
		);
		$room = $this->roomMapper->findOneByPublicId($publicId);

		$jwt = new JWT($jwtSecret, 'HS256');
		$token = $jwt->encode(
			[
				"context" => [
					"user" => [
						"name" => $displayName,
					],
				],
				"aud" => "jitsi",
				"iss" => "jitsi",
				"sub" => '*',
				"room" => $room->getPublicId(),
				"exp" => time() + 12 * 60 * 60,
			]
		);

		return new DataResponse(
			[
				'token' => $token,
			]
		);
	}
}
