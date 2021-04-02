<?php

namespace OCA\jitsi\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings
{

    /** @var IConfig */
    private $config;

    /** @var IL10N */
    private $l;

    /**
     * Admin constructor.
     *
     * @param IConfig $config
     * @param IL10N $l
     */
    public function __construct(
        IConfig $config,
        IL10N $l
    ) {
        $this->config = $config;
        $this->l = $l;
    }

    public function getForm()
    {
        $parameters = [
            'jitsi.jwt_secret' => $this->config->getAppValue(
                'jitsi',
                'jitsi.jwt_secret',
                ''
            ),
        ];

        return new TemplateResponse('jitsi', 'admin', $parameters);
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
