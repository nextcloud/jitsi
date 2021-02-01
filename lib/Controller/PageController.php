<?php

namespace OCA\jitsi\Controller;

use OCA\jitsi\Db\RoomMapper;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\FeaturePolicy;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

class PageController extends AbstractController
{
	/**
	 * @var RoomMapper
	 */
	private $roomMapper;

	/**
	 * @var IConfig
	 */
	private $config;

	public function __construct(
		$AppName,
		IRequest $request,
		RoomMapper $roomMapper,
		IUserSession $userSession,
		IConfig $config
	) {
		parent::__construct($AppName, $request, $userSession);
		$this->roomMapper = $roomMapper;
		$this->config = $config;
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse
	{
		if (($checkBrowserResult = $this->checkBrowser()) !== null) {
			return $checkBrowserResult;
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

		$room = $this->roomMapper->findOneByPublicId($publicId);
		$serverUrl = $this->config->getAppValue('jitsi', 'server_url');
		$helpLink = $this->config->getAppValue('jitsi', 'help_link');
		$loggedIn = $this->userSession->isLoggedIn();

		$renderAs = $loggedIn ? 'user' : 'public';

		$response = new TemplateResponse(
			'jitsi',
			'room',
			[
				'loggedIn'    => $loggedIn,
				'serverUrl'   => $serverUrl,
				'helpLink'    => $helpLink,
				'appUrl'      => $appUrl,
			],
			$renderAs
		);

		$this->setPolicies($response);
		return $response;
	}

	private function setPolicies(Response $response): void
	{
		$domain = $this->determineServiceUrl();
		$host = $this->determineJitsiHost();

		$csp = new ContentSecurityPolicy();
		$csp->addAllowedFrameDomain($host);
		$response->setContentSecurityPolicy($csp);

		$fp = new FeaturePolicy();
		$fp->addAllowedCameraDomain('https://nextcloud.local');
		$fp->addAllowedCameraDomain('https://' . $_SERVER['HTTP_HOST']);
		$fp->addAllowedCameraDomain($domain);
		$fp->addAllowedMicrophoneDomain('https://nextcloud.local');
		$fp->addAllowedMicrophoneDomain('https://' . $_SERVER['HTTP_HOST']);
		$fp->addAllowedMicrophoneDomain($domain);
		$response->setFeaturePolicy($fp);
	}

	private function determineServiceUrl(): string
	{
		return $this->config->getAppValue('jitsi', 'server_url');
	}

	private function determineJitsiHost(): string
	{
		$serverUrl = $this->determineServiceUrl();
		$urlParts = parse_url($serverUrl);
		return $urlParts['host'];
	}
}
