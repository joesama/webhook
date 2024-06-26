<?php

namespace Joesama\Webhook\Connectors;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractConnector
{
    /**
     * Web Hook connector id.
     *
     * @var string
     */
    private string $webHookConnectorId;

    /**
     * Initiate connector constructor
     */
    public function __construct()
    {
        $this->setConnectorId(strtolower(class_basename($this)));
    }

    /**
     * Define request content type.
     */
    public function webHookContentType(): string
    {
        return RequestOptions::JSON;
    }

    /**
     * Define additional handling HTTP request response.
     *
     * @return mixed
     */
    public function webHookResponse(ResponseInterface $response, RequestInterface $request)
    {
        $responseContent = $response->getBody()->getContents();

        if (($jsonResponse = json_decode($responseContent, true)) === null) {
            return $responseContent;
        }

        return $jsonResponse;
    }

    /**
     * Define additional handling for exceptions.
     *
     * @return mixed
     */
    public function webHookException(TransferException $exception, RequestInterface $request): mixed
    {
        if ($exception instanceof BadResponseException) {
            $response = new Response(
                $exception->getResponse()->getStatusCode(),
                $exception->getResponse()->getHeaders(),
                $exception->getResponse()->getBody()->getContents(),
                $exception->getResponse()->getProtocolVersion(),
                $exception->getResponse()->getReasonPhrase()
            );
        } else {
            $response = new Response(500, [], $exception->getMessage());
        }

        return $response;
    }

    /**
     * Save data to data storage.
     */
    public function webHookSavingData(array $logData): void
    {
        //@TODO: Implement saving logic here..
    }

    /**
     * Set connector id.
     */
    public function setConnectorId(string $connectorId): void
    {
        $this->webHookConnectorId = $connectorId;
    }

    /**
     * Set connector id.
     */
    public function getConnectorId(): ?string
    {
        return $this->webHookConnectorId;
    }
}
