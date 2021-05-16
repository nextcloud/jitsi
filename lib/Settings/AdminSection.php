<?php

namespace OCA\jitsi\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection
{
    /** @var IL10N */
    private $l;

    /** @var IURLGenerator */
    private $urlgen;

    public function __construct(IL10N $l, IURLGenerator $urlgen)
    {
        $this->l = $l;
        $this->urlgen = $urlgen;
    }

    public function getIcon(): string
    {
        return $this->urlgen->imagePath('jitsi', 'settings.svg');
    }

    public function getID(): string
    {
        return 'jitsi';
    }

    public function getName(): string
    {
        return 'Jitsi';
    }

    public function getPriority(): int
    {
        return 80;
    }
}
