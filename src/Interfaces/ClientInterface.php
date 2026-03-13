<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

interface ClientInterface
{
    public function get(string $endpoint, array $headers = []): ResponseInterface;

    /**
     * @param  string|resource|StreamInterface|null  $body
     */
    public function patch(string $endpoint, $body, array $headers = []): ResponseInterface;

    /**
     * @param  string|resource|StreamInterface|null  $body
     */
    public function post(string $endpoint, $body, array $headers = []): ResponseInterface;

    public function delete(string $endpoint, array $headers = []): ResponseInterface;

    public function getBaseUri(): string;

    public function setBaseUri(string $baseUri);
}
