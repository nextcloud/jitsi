<?php

/**
 * @var array $_
 */

// quick hacky low-tec info page about unsupported browsers

?>

<div
	style="width: 100vw; height: 100vh; background-color: #fff; padding-top: 150px; text-align: center;">
	<p style="font-size: 24px; color: #767676; margin-bottom: 24px;">
		Ihr Browser (<?php echo $_['browserName']; ?>) ist veraltet
		und<br>
		wird leider nicht mehr unterstützt
	</p>
	<p style="margin-bottom: 4px; color: #000;">
		Empfohlen wird die neuste Version eines dieser Browser:
	</p>
	<ul style="display: inline-block; text-align: left; color: #000;">
		<li>• Chrome</li>
		<li>• Chromium</li>
		<li>• Edge (ab 79)</li>
		<li>• Firefox</li>
		<li>• Safari (ab 10)</li>
	</ul>
</div>
