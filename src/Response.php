<?php

namespace Swis\JsonApi\Client;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Swis\JsonApi\Client\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var HttpResponseInterface
     */
    private $response;

    public function __construct(HttpResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasSuccessfulStatusCode(): bool
    {
        return $this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300;
    }

    /**
     * @return bool
     */
    public function hasServerErrorStatusCode(): bool
    {
        return $this->response->getStatusCode() >= 500 && $this->response->getStatusCode() < 600;
    }

    public function getResponse(): HttpResponseInterface
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasBody(): bool
    {
        return (bool)$this->response->getBody()->getSize();
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)$this->response->getBody();
    }

    /**
     * @param string $header
     *
     * @return bool
     */
    public function hasHeader(string $header): bool
    {
        return $this->response->hasHeader($header);
    }

    /**
     * @param string $header
     *
     * @return string
     */
    public function getHeader(string $header): string
    {
        return array_first($this->response->getHeader($header));
    }
}
