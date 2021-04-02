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
    data-server-url="<?= $_['serverUrl']; ?>"
    data-display-join-using-the-jitsi-app="<?= $_['display_join_using_the_jitsi_app'] ? 'true' : 'false'; ?>">
</div>
