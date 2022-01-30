<?php

declare(strict_types=1);

use OCP\IL10N;

/**
 * quick hacky low-tec info page about unsupported browsers
 *
 * @var array $_
 * @var IL10N $l
 */

?>

<div
    style="width: 100vw; height: 100vh; background-color: #fff; padding-top: 150px; text-align: center;">
    <p style="font-size: 24px; color: #767676; margin-bottom: 24px;">
        <?= $l->t('Your browser (%1$s) is outdated and<br>no longer supported', $_['browserName']); ?>
    </p>
    <p style="margin-bottom: 4px; color: #000;">
        <?php p($l->t('It is recommended to use the latest version of one of the following browsers:')); ?>
    </p>
    <ul style="display: inline-block; text-align: left; color: #000;">
        <li>• Chrome</li>
        <li>• Chromium</li>
        <li>• Edge (>= 79)</li>
        <li>• Firefox</li>
        <li>• Safari (=> 10)</li>
    </ul>
</div>
