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

    public function getIcon()
    {
        return $this->urlgen->imagePath('jitsi', 'settings.svg');
    }

    public function getID()
    {
        return 'jitsi';
    }

    public function getName()
    {
        return 'Jitsi';
    }

    public function getPriority()
    {
        return 80;
    }
}
