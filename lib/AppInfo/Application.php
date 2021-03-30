<?php

namespace OCA\jitsi\AppInfo;

use OCA\jitsi\Search\Provider;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {

	public const APP_ID = 'jitsi';
	public const APP_NAME = 'Jitsi Integration';

	public const SETTING_SERVER_URL = 'server_url';
	public const SETTING_JWT_SECRET = 'jwt_secret';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		require_once __DIR__ . '/../../vendor/autoload.php';
		$context->registerSearchProvider(Provider::class);
	}

	public function boot(IBootContext $context): void {
		// TODO: Implement boot() method.
	}
}
