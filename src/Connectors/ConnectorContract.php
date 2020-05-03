<?php
namespace Joesama\Webhook\Connectors;

use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ConnectorContract
{
    /**
     * Get connector id.
     *
     * @return string|null
     */
    public function getConnectorId(): ?string;

    /**
     * Set connector id.
     *
     * @return void
     */
    public function setConnectorId(string $id): void;

    /**
     * Define configuration parameter to be attach to request.
     *
     * @return array
     */
    public function webHookConfiguration(): array;

    /**
     * Define request content to be send.
     *
     * @return array
     */
    public function webHookContent(): array;

    /**
     * Define request type.
     *
     * @return string
     */
    public function webHookContentType(): string;

    /**
     * Define request header to attach to request.
     *
     * @return array
     */
    public function webHookHeader(): array;

    /**
     * Define additional handling before exceptions thrown.
     * By default WebHookException will be thrown afterward.
     *
     * @param TransferException $exception
     * @param RequestInterface $request
     * @return mixed
     */
    public function webHookException(TransferException $exception, RequestInterface $request);

    /**
     * Define handling HTTP request response.
     *
     * @param ResponseInterface $response
     * @param RequestInterface  $request
     * @return mixed
     */
    public function webHookResponse(ResponseInterface $response, RequestInterface $request);

    /**
     * Save data to data storage.
     *
     * @param array $logData
     * @return void
     */
    public function webHookSavingData(array $logData): void;
}
