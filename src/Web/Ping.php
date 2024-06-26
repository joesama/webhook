<?php

namespace Joesama\Webhook\Web;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Ping
{
    /**
     * Client configuration.
     *
     * @var array
     */
    protected array $configs = [];

    /**
     * Request method..
     *
     * @var string
     */
    protected string $method;

    /**
     * Endpoint base URI.
     *
     * @var string
     */
    protected string $pathUri;

    /**
     * Request payload parameters.
     *
     * @var array
     */
    protected array $options;

    /**
     * Request parameter.
     *
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * Dispatch HTTP request.
     */
    final protected function dispatch(): ResponseInterface
    {
        $client = new Client($this->configs);

        $this->setPsr7RequestHeader();

        try {
            return $client->request(
                $this->method,
                $this->pathUri,
                $this->options
            );
        } catch (TransferException $exception) {
            return $this->exceptionHandlers($exception);
        }
    }

    /**
     * Make the client parameter acessible.
     */
    final protected function getPsr7RequestHeader(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Set request parameter.
     */
    private function setPsr7RequestHeader(): void
    {
        $this->request = new Request($this->method, $this->pathUri, $this->configs);
    }

    /**
     * Optional handler for exception.
     */
    abstract protected function exceptionHandlers(TransferException $exception);
}
