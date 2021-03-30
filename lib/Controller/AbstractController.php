<?php

namespace OCA\jitsi\Controller;

use Browser;
use OCA\jitsi\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;

abstract class AbstractController extends Controller
{
	/**
	 * @var IUserSession
	 */
	protected $userSession;

	/**
	 * @var IConfig
	 */
	protected $config;

	public function __construct(
		string $AppName,
		IRequest $request,
		IUserSession $userSession,
		IConfig $config
	) {
		parent::__construct($AppName, $request);
		$this->userSession = $userSession;
		$this->config = $config;
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

	private function gatherBrowserInfo(): array
	{
		$browser = new Browser();
		$browserName = $browser->getBrowser();
		$browserVersion = $browser->getVersion();

		return [
			'name' => sprintf(
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

	protected function checkConfigured(): ?TemplateResponse
	{
		if ($this->isConfigured()) {
			return null;
		}

		$loggedIn = $this->userSession->isLoggedIn();
		$renderAs = $loggedIn ? 'user' : 'public';

		return new TemplateResponse(
			'jitsi',
			'incomplete_settings',
			[],
			$renderAs
		);
	}

	private function isConfigured(): bool
	{
		$serverUrl = $this->config->getAppValue(
			Application::APP_ID,
			Application::SETTING_SERVER_URL,
			null
		);

		$jwtSecret = $this->config->getAppValue(
			Application::APP_ID,
			Application::SETTING_JWT_SECRET,
			null
		);

		return !empty($serverUrl) && !empty($jwtSecret);
	}
}
