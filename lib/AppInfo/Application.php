<?php

declare(strict_types=1);

namespace OCA\jitsi\AppInfo;

use OCA\jitsi\Config\Config;
use OCA\jitsi\Search\Provider;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'jitsi';
	public const APP_NAME = 'Jitsi Integration';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		require_once __DIR__ . '/../../vendor/autoload.php';
		$context->registerSearchProvider(Provider::class);
	}

	public function boot(IBootContext $context): void {
		$this->setUpJitsiServerUrl($context);
	}

	private function setUpJitsiServerUrl(IBootContext $context): void {
		/** @var Config $config */
		$config = $context->getAppContainer()->query(Config::class);

		$serverUrl = $config->jitsiServerUrl();

		if (empty($serverUrl)) {
			$config->updateJitsiServerUrl('https://fairmeeting.net/');
			return;
		}

		if (substr($serverUrl, -1) !== '/') {
			$config->updateJitsiServerUrl($serverUrl . '/');
		}
	}
}
