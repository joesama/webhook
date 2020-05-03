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
        $this->configurable = Collection::make([]);

        if (is_string($config)) {
            $this->mapConfiguration(
                config($config, config(self::CONF_DIR . $config))
            );
        } else {
            $this->configurable = $this->configurable->merge($config);
        }

        $this->configs = $this->excludeConfigurable();
    }

    /**
     * Map configuration parameter to it domain.
     *
     * @param array $config
     * @return void
     */
    private function mapConfiguration(array $config = []): void
    {
        $this->configurable = $this->configurable->merge($config);

        $this->hooks = $this->configurable->get(self::REQUEST_URI);
    }

    /**
     * Exclude configurable from body
     *
     * @return array
     */
    protected function excludeConfigurable(): array
    {
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
