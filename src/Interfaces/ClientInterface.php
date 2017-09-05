<?php

namespace Swis\JsonApi\Interfaces;

interface ClientInterface
{
    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function get(string $endpoint, array $headers = []);

    /**
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function patch(string $endpoint, $body, array $headers = []);

    /**
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function post(string $endpoint, $body, array $headers = []);

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function delete(string $endpoint, array $headers = []);

    /**
     * @return string
     */
    public function getBaseUri(): string;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri);
}
