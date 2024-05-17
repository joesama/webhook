<?php

namespace Joesama\Webhook\Web;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;

class Config
{
    /**
     * Default webhook config directory.
     */
    public const CONF_DIR = 'webhooks.';

    /**
     * Default webhook request key.
     */
    public const REQUEST_URI = 'request';

    /**
     * Http configuration parameters.
     *
     * @var array
     */
    public array $configs;

    /**
     * WebHook configuration parameters.
     *
     * @var array
     */
    public array $hooks;

    /**
     * Configuration parameters.
     *
     * @var Collection
     */
    private Collection $configurable;

    public function __construct($config)
    {
        if (is_string($config)) {
            $config = config($config, config(self::CONF_DIR . $config, []));
        }

        $this->configurable = new Collection($config);

        $this->configs = $this->excludeBodyConfig();

        $this->hooks = $this->configurable->get(self::REQUEST_URI, []);
    }

    /**
     * Exclude configurable from body
     */
    protected function excludeBodyConfig(): array
    {
        return $this->configurable->except([
            RequestOptions::BODY,
            RequestOptions::FORM_PARAMS,
            RequestOptions::JSON,
            RequestOptions::MULTIPART,
            RequestOptions::QUERY,
            self::REQUEST_URI,
        ])->toArray();
    }

    /**
     * Provide configuration parameter as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->configurable->toArray();
    }
}
