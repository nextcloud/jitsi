<?php

namespace OCA\jitsi\Config;

use OCA\jitsi\AppInfo\Application;
use OCP\IConfig;

class Config
{
    public const KEY_JITSI_SERVER_URL = 'jitsi_server_url';
    public const KEY_JWT_SECRET = 'jwt_secret';
    public const KEY_HELP_LINK = 'help_link';
    public const KEY_DISPLAY_JOIN_USING_THE_JITSI_APP = 'display_join_using_the_jitsi_app';

    public const BOOL_TRUE = '1';
    public const BOOL_FALSE = '0';

    /**
     * @var IConfig
     */
    private $config;

    public function __construct(IConfig $config)
    {
        $this->config = $config;
    }

    public function jitsiServerUrl(): ?string
    {
        return $this->readString(self::KEY_JITSI_SERVER_URL);
    }

    public function jwtSecret(): ?string
    {
        return $this->readString(self::KEY_JWT_SECRET);
    }

    public function helpLink(): ?string
    {
        return $this->readString(self::KEY_HELP_LINK);
    }

    public function displayJoinUsingTheJitsiApp(): bool
    {
        // @phpstan-ignore-next-line
        return $this->readBool(self::KEY_DISPLAY_JOIN_USING_THE_JITSI_APP, true);
    }

    private function readBool(string $key, ?bool $default = null): ?bool
    {
        $value = $this->config->getAppValue(
            Application::APP_ID,
            $key,
            ''
        );

        return $value === '' ? $default : $value === self::BOOL_TRUE;
    }

    private function readString(string $key): ?string
    {
        $value = $this->config->getAppValue(
            Application::APP_ID,
            $key,
            ''
        );

        return $value === '' ? null : $value;
    }
}
