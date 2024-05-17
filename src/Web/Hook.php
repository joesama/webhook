<?php

namespace Joesama\Webhook\Web;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Joesama\Webhook\Connectors\ConnectorContract;
use Joesama\Webhook\Exceptions\WebHookException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Hook extends Ping
{
    /**
     * WebHook request configuration.
     *
     * @var array
     */
    private array $hooks;

    /**
     * Implementation of ConnectorContract.
     *
     * @var ConnectorContract|null
     */
    private ?ConnectorContract $connector = null;

    /**
     * Connector id definition.
     *
     * @var string
     */
    private string $connectorId;

    /**
     * WebHook constructor.
     *
     * @param ConnectorContract|array|string $config |string $config
     */
    public function __construct(ConnectorContract|array|string $config = [])
    {
        $this->configs = ($config instanceof ConnectorContract) ?
            $this->connectorConfigurable($config) :
            $this->webHookConfigurable($config);
    }

    /**
     * Set the request body parameter.
     */
    public function setRequestBody(array $request, string $type = 'json'): self
    {
        $this->mapContentType($type);

        $this->options[$type] = $request;

        return $this;
    }

    /**
     * Set request header parameter.
     */
    public function setRequestHeader(array $headers): self
    {
        $this->configs[RequestOptions::HEADERS] = array_merge(
            Arr::get($this->configs, RequestOptions::HEADERS, []),
            $headers
        );

        return $this;
    }

    /**
     * Attached configuration parameters.
     *
     * @param array|string $config
     * @return Hook
     */
    public function configurable(array|string $config): self
    {
        $this->configs = $this->webHookConfigurable($config);

        return $this;
    }

    /**
     * Get response from end point.
     *
     * @param string|null $url Endpoint URL
     * @param string $method Request method
     * @return mixed
     */
    public function getResponse(string $url = null, string $method = 'POST')
    {
        $this->setUrlRequest($method, $url);

        return $this->responseHandler($this->dispatch(), $this->getPsr7RequestHeader());
    }

    /**
     * Prepare configuration parameter.
     *
     * @param array|string $config
     * @return array
     */
    private function webHookConfigurable(array|string $config): array
    {
        if (is_string($config)) {
            $hookConfig = new Config($config);

            $config = $hookConfig->configs;

            $this->hooks = $hookConfig->hooks;
        }

        return array_merge($config, $this->configs);
    }

    /**
     *  Implement WebHookConnector definition.
     *
     * @param ConnectorContract $connector
     * @return void
     */
    private function implementWebHookConnector(ConnectorContract $connector): void
    {
        $this->connector = $connector;

        $this->connectorId = $connector->getConnectorId();
    }

    /**
     * Map request content with header content type.
     *
     * @param string $type
     * @return string|null
     */
    private function mapContentType(string $type): ?string
    {
        $default = [
            RequestOptions::JSON => 'application/json',
            RequestOptions::MULTIPART => 'multipart/form-data',
            RequestOptions::FORM_PARAMS => 'application/x-www-form-urlencoded',
        ];

        return Arr::get($default, strtolower($type), null);
    }

    /**
     * Set url & request method from webhook configurable.
     *
     * @param string $method
     * @param string|null $url
     * @return void
     */
    private function setUrlRequest(string $method, ?string $url): void
    {
        $this->method = $method;

        $this->pathUri = Str::contains($url, '/') ? $url : Arr::get($this->hooks, $url. '.' .$method, $url);
    }

    /**
     * WebHook HTTP response handler.
     *
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @return mixed
     */
    private function responseHandler(ResponseInterface $response, RequestInterface $request): mixed
    {
        if ($this->connector instanceof ConnectorContract) {
            return $this->connector->webHookResponse($response, $request);
        }

        return $response;
    }

    /**
     * WebHook HTTP request exception handler.
     *
     *
     * @param TransferException $exception
     * @return mixed
     *
     * @throws WebHookException
     */
    protected function exceptionHandlers(TransferException $exception): mixed
    {
        $this->logExceptionError($exception);

        if ($this->connector) {
            return $this->connector->webHookException($exception, $this->getPsr7RequestHeader());
        }

        throw new WebHookException($exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * Log error exception produce.
     */
    private function logExceptionError(TransferException $exception): void
    {
        $logData = $this->logDataFormat();

        $logData['response'] = $exception->getMessage();

        if ($this->connector instanceof ConnectorContract) {
            //Need to notify system log error has occurs as the exception are handles.
            $this->notifyCriticalLog($exception, $logData);
        }

        $this->saveLogToDatabase($logData);
    }

    /**
     * Sent critical notification to application log.
     */
    private function notifyCriticalLog(TransferException $exception, array $logData): void
    {
        Log::critical(
            $this->connectorId,
            [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'url' => $logData['endpoint'],
                'method' => $this->method,
            ]
        );
    }

    /**
     * Save the log data to data base.
     */
    private function saveLogToDatabase(array $logData)
    {
        if ($this->connector instanceof ConnectorContract) {
            //Saving log to data storage or etc...
            $this->connector->webHookSavingData($logData);
        }
    }

    /**
     * Format the log data.
     */
    private function logDataFormat(): array
    {
        $request = $this->getPsr7RequestHeader();

        return [
            'method' => $this->method,
            'endpoint' => Arr::first($request->getHeader('base_uri')) . $request->getUri()->getPath(),
            'request' => array_merge($this->configs, $this->options),
            'response' => null,
        ];
    }

    /**
     * @param ConnectorContract $connector
     * @return array
     */
    protected function connectorConfigurable(ConnectorContract $connector):array
    {
        $this->implementWebHookConnector($connector);

        $configs = $this->webHookConfigurable($connector->webHookConfiguration());

        $configs[RequestOptions::HEADERS] = array_merge(
            Arr::get($this->configs, RequestOptions::HEADERS, []),
            $connector->webHookHeader(),
            ['Content-type' => $this->mapContentType($connector->webHookContentType())]
        );

        $this->options[$connector->webHookContentType()] = $connector->webHookContent();

        return $configs;
    }
}
