<?php
namespace Joesama\Webhook\Web;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Joesama\Webhook\Web\Ping;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\TransferException;
use Joesama\Webhook\Exceptions\WebHookException;
use Joesama\Webhook\Connectors\ConnectorContract;

class Hook extends Ping
{
    /**
     * WebHook request configuration.
     *
     * @var array
     */
    private $hooks;

    /**
     * Implementation of ConnectorContract.
     *
     * @var ConnectorContract
     */
    private $connector;

    /**
     * Connector id definition.
     *
     * @var string
     */
    private $connectorId;

    /**
     * WebHook constructor.
     *
     * @param ConnectorContract|array $config|string $config
     */
    public function __construct($config = [])
    {
        if ($config instanceof ConnectorContract) {
            $this->implementWebHookConnector($config);
        } else {
            $this->configs = $this->webHookConfigurable($config);
        }
    }

    /**
     * Set the request body parameter.
     *
     * @param array  $request
     * @param string $type
     * @return Hook
     */
    public function setRequestBody(array $request, string $type = 'json'): self
    {
        $this->mapContentType($type);

        $this->options[$type] = $request;

        return $this;
    }

    /**
     * Set request header parameter.
     *
     * @param array $headers
     * @return Hook
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
     * @param string|array $config
     * @return Hook
     */
    public function configurable($config): self
    {
        $this->configs = $this->webHookConfigurable($config);

        return $this;
    }

    /**
     * Get response from end point.
     *
     * @param string $url    Endpoint URL
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
    private function webHookConfigurable($config): array
    {
        if (is_string($config)) {
            $hookConfig = new Config($config);

            $config = $hookConfig->configs;

            $this->hooks = $hookConfig->hooks;
        }

        return array_merge($config, $this->configs);
    }

    /**
     * Implement WebHookConnector definition.
     *
     * @param ConnectorContract $connector
     */
    private function implementWebHookConnector(ConnectorContract $connector): void
    {
        $this->connector = $connector;

        $connector->setConnectorId(class_basename($connector));

        $this->connectorId = $connector->getConnectorId();

        $this->configs = $this->webHookConfigurable($connector->webHookConfiguration());

        $this->mapContentType($connector->webHookContentType());

        $this->configs[RequestOptions::HEADERS] = array_merge(
            Arr::get($this->configs, RequestOptions::HEADERS, []),
            $connector->webHookHeader()
        );

        $this->options[$connector->webHookContentType()] = $connector->webHookContent();
    }

    /**
     * Map request content with header content type.
     *
     * @param string $type
     */
    private function mapContentType(string $type): void
    {
        $default = [
            RequestOptions::JSON => 'application/json',
            RequestOptions::MULTIPART => 'multipart/form-data',
            RequestOptions::FORM_PARAMS => 'application/x-www-form-urlencoded',
        ];

        if (($contentType = Arr::get($default, strtolower($type), null)) !== null) {
            $this->configs[RequestOptions::HEADERS]['Content-type'] = $contentType;
        }
    }

    /**
     * Set url & request method from webhook configurable.
     *
     * @param string|null $method
     * @param string|null $url
     */
    private function setUrlRequest(string $method, ?string $url): void
    {
        $this->method = $method;

        $this->pathUri = Str::contains($url, '/') ? $url : Arr::get($this->hooks, $url . '.' . $method, $url);
    }

    /**
     * WebHook HTTP response handler.
     *
     * @param ResponseInterface $response
     * @param RequestInterface  $request
     * @return mixed
     */
    private function responseHandler(ResponseInterface $response, RequestInterface $request)
    {
        if ($this->connector instanceof ConnectorContract) {
            return $this->connector->webHookResponse($response, $request);
        }

        return $response;
    }

    /**
     * WebHook HTTP request exception handler.
     *
     * @param $exception
     * @return mixed
     * @throws WebHookException
     */
    protected function exceptionHandlers($exception)
    {
        $this->logExceptionError($exception);

        if ($this->connector instanceof ConnectorContract) {
            return $this->connector->webHookException($exception, $this->getPsr7RequestHeader());
        }
 
        throw new WebHookException($exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * Log error exception produce.
     *
     * @param TransferException $exception
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
     *
     * @param TransferException $exception
     * @param array             $logData
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
                'method' => $this->method
            ]
        );
    }

    /**
     * Save the log data to data base.
     *
     * @param array $logData
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
     *
     * @return array
     */
    private function logDataFormat(): array
    {
        $request = $this->getPsr7RequestHeader();

        return [
            'method' => $this->method,
            'endpoint' => Arr::first($request->getHeader('base_uri')) . $request->getUri()->getPath(),
            'request' => array_merge($this->configs, $this->options),
            'response' => null
        ];
    }
}
