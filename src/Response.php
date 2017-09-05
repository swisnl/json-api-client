<?php

namespace Swis\JsonApi;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Swis\JsonApi\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var array
     */
    protected $successfulStatusCodes = [
        200,
        202,
        204,
    ];

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    private $guzzleResponse;

    /**
     * @param \GuzzleHttp\Psr7\Response $guzzleResponse
     */
    public function __construct(GuzzleResponse $guzzleResponse)
    {
        $this->guzzleResponse = $guzzleResponse;
    }

    /**
     * @return bool
     */
    public function hasSuccessfulStatusCode(): bool
    {
        return in_array($this->guzzleResponse->getStatusCode(), $this->successfulStatusCodes, false);
    }

    /**
     * @return bool
     */
    public function hasServerErrorStatusCode(): bool
    {
        return $this->guzzleResponse->getStatusCode() >= 500 && $this->guzzleResponse->getStatusCode() < 600;
    }

    /**
     * @return bool
     */
    public function hasBody(): bool
    {
        return (bool)$this->guzzleResponse->getBody()->getSize();
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)$this->guzzleResponse->getBody();
    }

    /**
     * @param string $header
     *
     * @return bool
     */
    public function hasHeader(string $header): bool
    {
        return $this->guzzleResponse->hasHeader($header);
    }

    /**
     * @param string $header
     *
     * @return string
     */
    public function getHeader(string $header): string
    {
        return array_first($this->guzzleResponse->getHeader($header));
    }
}
