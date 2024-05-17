<?php

namespace Joesama\Webhook\Connectors;

use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ConnectorContract
{
    /**
     * Get connector id.
     */
    public function getConnectorId(): ?string;

    /**
     * Set connector id.
     */
    public function setConnectorId(string $id): void;

    /**
     * Define configuration parameter to be attach to request.
     */
    public function webHookConfiguration(): array;

    /**
     * Define request content to be send.
     */
    public function webHookContent(): array;

    /**
     * Define request type.
     */
    public function webHookContentType(): string;

    /**
     * Define request header to attach to request.
     */
    public function webHookHeader(): array;

    /**
     * Define additional handling before exceptions thrown.
     * By default WebHookException will be thrown afterward.
     *
     * @return mixed
     */
    public function webHookException(TransferException $exception, RequestInterface $request);

    /**
     * Define handling HTTP request response.
     *
     * @return mixed
     */
    public function webHookResponse(ResponseInterface $response, RequestInterface $request);

    /**
     * Save data to data storage.
     */
    public function webHookSavingData(array $logData): void;
}
