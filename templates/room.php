<?php

use OCA\jitsi\AppInfo\Application;

/**
 * @var array $_
 */

script(Application::APP_ID, 'room');

?>

<div
	id="jitsi"
	data-help-link="<?= $_['helpLink'] ?>"
	data-server-url="<?= $_['serverUrl']; ?>">
</div>
