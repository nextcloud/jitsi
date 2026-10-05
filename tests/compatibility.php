<?php

declare(strict_types=1);

// Standalone API smoke test: no database, installed server or credentials needed.
$server = $argv[1] ?? '';
if (!is_file($server . '/version.php')) {
	fwrite(STDERR, "Usage: php tests/compatibility.php /path/to/nextcloud-source\n");
	exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(static function (string $class) use ($server): void {
	$prefixes = [
		'OCP\\' => $server . '/lib/public/',
		'OCA\\jitsi\\' => __DIR__ . '/../lib/',
	];
	foreach ($prefixes as $prefix => $directory) {
		if (strpos($class, $prefix) === 0) {
			$file = $directory . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
			if (is_file($file)) {
				require_once $file;
			}
		}
	}
});

function check(bool $condition, string $message): void {
	if (!$condition) {
		throw new RuntimeException($message);
	}
}

// Loading the classes checks their inheritance and Nextcloud interface signatures.
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../lib')) as $file) {
	if ($file->getExtension() !== 'php') {
		continue;
	}
	$relative = substr($file->getPathname(), strlen(__DIR__ . '/../lib/'), -4);
	$class = 'OCA\\jitsi\\' . str_replace('/', '\\', $relative);
	check(class_exists($class), 'Cannot load ' . $class);
}

$room = new OCA\jitsi\Db\Room();
$room->setId(1);
$room->setName('Compatibility test');
$room->setCreatorId('owner');
$room->setPublicId('public-id');
check($room->jsonSerialize() === [
	'id' => 1,
	'name' => 'Compatibility test',
	'publicId' => 'public-id',
], 'Room serialization changed');

// Exercise the app's policy generation against the real Nextcloud response APIs.
$config = new class extends OCA\jitsi\Config\Config {
	public function __construct() {
	}

	public function jitsiServerUrl(): ?string {
		return 'https://meet.example.org/';
	}
};
$controller = new class($config) extends OCA\jitsi\Controller\PageController {
	public function __construct(OCA\jitsi\Config\Config $config) {
		$this->appConfig = $config;
	}
};
$_SERVER['HTTP_HOST'] = 'cloud.example.org';
// Nextcloud 25's constructor needs OC::$server to populate request headers.
// This standalone test only exercises policy setters/getters, not headers.
$response = (new ReflectionClass(OCP\AppFramework\Http\Response::class))->newInstanceWithoutConstructor();
$setPolicies = new ReflectionMethod(OCA\jitsi\Controller\PageController::class, 'setPolicies');
$setPolicies->invoke($controller, $response);
check(strpos($response->getContentSecurityPolicy()->buildPolicy(), 'meet.example.org') !== false, 'Jitsi frame blocked by CSP');
$policy = $response->getFeaturePolicy()->buildPolicy();
check(strpos($policy, 'camera') !== false && strpos($policy, 'microphone') !== false, 'Media permissions missing');
check(strpos($policy, 'https://meet.example.org/') !== false, 'Jitsi media domain missing');

$jwt = new Ahc\Jwt\JWT('compatibility-test-secret', 'HS256');
$token = $jwt->encode(['room' => 'test-room', 'exp' => time() + 60]);
check($jwt->decode($token)['room'] === 'test-room', 'JWT round trip failed');
check(Ramsey\Uuid\Uuid::isValid(Ramsey\Uuid\Uuid::uuid4()->toString()), 'Room UUID generation failed');
check(is_string((new Browser())->getBrowser()), 'Browser detection failed');

require $server . '/version.php';
printf("API smoke checks passed on Nextcloud %s (PHP %s)\n", $OC_VersionString, PHP_VERSION);
