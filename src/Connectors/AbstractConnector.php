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
    private $webHookConnectorId;

    /**
     * Define request content type.
     *
     * @return string
     */
    public function webHookContentType(): string
    {
        return RequestOptions::JSON;
    }

    /**
     * Define additional handling HTTP request response.
     *
     * @param ResponseInterface $response
     * @param RequestInterface  $request
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
     * @param TransferException $exception
     * @param RequestInterface  $request
     * @return mixed
     */
    public function webHookException(TransferException $exception, RequestInterface $request)
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
     *
     * @param array $logData
     * @return void
     */
    public function webHookSavingData(array $logData): void
    {
        //@TODO: Implement saving logic here..
    }

    /**
     * Set connector id.
     *
     * @param string $connectorId
     */
    public function setConnectorId(string $connectorId): void
    {
        $this->webHookConnectorId = $connectorId;
    }

    /**
     * Set connector id.
     *
     * @return string|null
     */
    public function getConnectorId(): ?string
    {
        return $this->webHookConnectorId;
    }
}
