<?php

namespace Swis\JsonApi\Client\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get(string $endpoint, array $headers = []): ResponseInterface;

    /**
     * @param string                                                                         $endpoint
     * @param resource|string|int|float|bool|\Psr\Http\Message\StreamInterface|callable|null $body
     * @param array                                                                          $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function patch(string $endpoint, $body, array $headers = []): ResponseInterface;

    /**
     * @param string                                                                         $endpoint
     * @param resource|string|int|float|bool|\Psr\Http\Message\StreamInterface|callable|null $body
     * @param array                                                                          $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post(string $endpoint, $body, array $headers = []): ResponseInterface;

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete(string $endpoint, array $headers = []): ResponseInterface;

    /**
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri);
}
