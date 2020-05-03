<?php
namespace Joesama\Webhook\Examples;

use Joesama\Webhook\Connectors\AbstractConnector;
use Joesama\Webhook\Connectors\ConnectorContract;

class ExamplesConnector extends AbstractConnector implements ConnectorContract
{
    /**
    * Define configuration parameter to be attach to request.
    *
    * @return array
    */
    public function webHookConfiguration(): array
    {
        return [];
    }

    /**
     * Define request content to be send.
     *
     * @return array
     */
    public function webHookContent(): array
    {
        return [];
    }
    
    /**
     * Define request header to attach to request.
     *
     * @return array
     */
    public function webHookHeader(): array
    {
        return [];
    }
}
