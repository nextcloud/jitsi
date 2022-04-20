<?php

declare(strict_types=1);

namespace OCA\jitsi\Settings;

use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {
	/** @var IURLGenerator */
	private $urlgen;

	public function __construct(IURLGenerator $urlgen) {
		$this->urlgen = $urlgen;
	}

	public function getIcon(): string {
		return $this->urlgen->imagePath('jitsi', 'settings.svg');
	}

	public function getID(): string {
		return 'jitsi';
	}

	public function getName(): string {
		return 'Jitsi';
	}

	public function getPriority(): int {
		return 80;
	}
}
