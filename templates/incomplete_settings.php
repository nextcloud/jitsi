<?php

use OCA\jitsi\AppInfo\Application;
use OCP\IL10N;

/**
 * @var array $_
 * @var IL10N $l
 */

style(Application::APP_ID, 'styles');

?>

<div class="jitsi-info-container">
	<h1 class="jitsi-info-title">
		<?php p($l->t('Conferences app not yet configured')); ?>
	</h1>
	<p class="jitsi-info-text">
		<?php p($l->t('Please contact your administrator to set up the conferences app.')); ?>
	</p>
</div>
