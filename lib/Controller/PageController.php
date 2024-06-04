<?php

declare(strict_types=1);

namespace OCA\jitsi\Controller;

use OCA\jitsi\Config\Config;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\FeaturePolicy;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IUserSession;

class PageController extends AbstractController {
	public function __construct(
		string $AppName,
		IRequest $request,
		IUserSession $userSession,
		Config $appConfig
	) {
		parent::__construct($AppName, $request, $userSession, $appConfig);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
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
	public function blank(): TemplateResponse {
		return new TemplateResponse('jitsi', 'blank');
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function room(string $publicId): TemplateResponse {
		if (($checkBrowserResult = $this->checkBrowser()) !== null) {
			return $checkBrowserResult;
		}

		$loggedIn = $this->userSession->isLoggedIn();
		$renderAs = $loggedIn ? 'user' : 'public';

		$response = new TemplateResponse(
			'jitsi',
			'room',
			[
				'loggedIn' => $loggedIn,
				'serverUrl' => $this->appConfig->jitsiServerUrl(),
				'helpLink' => $this->appConfig->helpLink(),
				'display_join_using_the_jitsi_app' => $this->appConfig->displayJoinUsingTheJitsiApp(),
				'display_all_sharing_invites' =>  $this->appConfig->displayAllSharingInvites(),
			],
			$renderAs
		);

		$this->setPolicies($response);
		return $response;
	}

	private function setPolicies(Response $response): void {
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

	private function determineJitsiHost(): ?string {
		$serverUrl = $this->appConfig->jitsiServerUrl();

		if ($serverUrl === null) {
			return null;
		}

		$urlParts = parse_url($serverUrl);
		// @phpstan-ignore-next-line
		return $urlParts['host'];
	}
}
