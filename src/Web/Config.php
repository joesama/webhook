<?php
namespace Joesama\Webhook\Web;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;

class Config
{
    /**
     * Default webhook config directory.
     */
    const CONF_DIR = 'webhooks.';

    /**
     * Default webhook request key.
     */
    const REQUEST_URI = 'request';

    /**
     * Http configuration parameters.
     *
     * @var array
     */
    public $configs;

    /**
     * WebHook configuration parameters.
     *
     * @var array
     */
    public $hooks;

    /**
     * Configuration parameters.
     *
     * @var Collection
     */
    private $configurable;

    public function __construct($config)
    {
        if (is_string($config)) {
            $config = $this->mapConfiguration(
                config($config, config(self::CONF_DIR . $config))
            );
        }

        $this->configs = $config;
    }

    /**
     * Map configuration parameter to it domain.
     *
     * @param array $config
     * @return array
     */
    private function mapConfiguration(array $config = []): array
    {
        if (empty($config)) {
            return [];
        }

        $this->configurable = collect($config);

        $this->hooks = $this->configurable->get(self::REQUEST_URI);

        return $this->configurable->except([
            RequestOptions::BODY,
            RequestOptions::FORM_PARAMS,
            RequestOptions::JSON,
            RequestOptions::MULTIPART,
            RequestOptions::QUERY,
            self::REQUEST_URI
        ])->toArray();
    }

    /**
     * Provide configuration parameter as array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->configurable->toArray();
    }
}