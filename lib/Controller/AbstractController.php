<?php

namespace OCA\jitsi\Controller;

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

	public function __construct(
		string $AppName,
		IRequest $request,
		IUserSession $userSession
	) {
		parent::__construct($AppName, $request);
		$this->userSession = $userSession;
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
		$browser = new \Browser();
		return [
			'name' => sprintf('%s %s', $browser->getBrowser(), $browser->getVersion()),
			'supported' => !(
				$browser->getBrowser() === \Browser::BROWSER_IE ||
				($browser->getBrowser() === \Browser::BROWSER_EDGE && $browser->getVersion() < 79) ||
				($browser->getBrowser() === \Browser::BROWSER_SAFARI && $browser->getVersion() < 10)
			)
		];
	}
}
