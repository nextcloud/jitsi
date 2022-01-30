<?php

declare(strict_types=1);

namespace OCA\jitsi\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings
{
    public function getForm()
    {
        return new TemplateResponse('jitsi', 'admin', []);
    }

    public function getSection()
    {
        return 'jitsi';
    }

    public function getPriority()
    {
        return 50;
    }
}
