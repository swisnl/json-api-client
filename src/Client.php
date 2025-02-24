<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Swis\JsonApi\Client\Interfaces\ClientInterface;

class Client implements ClientInterface
{
    private HttpClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    private string $baseUri = '';

    private array $defaultHeaders = [
        'Accept' => 'application/vnd.api+json',
        'Content-Type' => 'application/vnd.api+json',
    ];

    public function __construct(
        ?HttpClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
    ) {
        $this->client = $client ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    public function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    public function setDefaultHeaders(array $defaultHeaders): void
    {
        $this->defaultHeaders = $defaultHeaders;
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function get(string $endpoint, array $headers = []): ResponseInterface
    {
        return $this->request('GET', $endpoint, null, $headers);
    }

    /**
     * @param  string|resource|\Psr\Http\Message\StreamInterface|null  $body
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function post(string $endpoint, $body, array $headers = []): ResponseInterface
    {
        return $this->request('POST', $endpoint, $body, $headers);
    }

    /**
     * @param  string|resource|\Psr\Http\Message\StreamInterface|null  $body
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function patch(string $endpoint, $body, array $headers = []): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, $body, $headers);
    }

    /**
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function delete(string $endpoint, array $headers = []): ResponseInterface
    {
        return $this->request('DELETE', $endpoint, null, $headers);
    }

    /**
     * @param  string|resource|\Psr\Http\Message\StreamInterface|null  $body
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function request(string $method, string $endpoint, $body = null, array $headers = []): ResponseInterface
    {
        return $this->client->sendRequest($this->buildRequest($method, $endpoint, $body, $headers));
    }

    /**
     * @param  string|resource|\Psr\Http\Message\StreamInterface|null  $body
     */
    protected function buildRequest(string $method, string $endpoint, $body = null, array $headers = []): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $this->getEndpoint($endpoint));

        if ($body !== null) {
            if (is_resource($body)) {
                $body = $this->streamFactory->createStreamFromResource($body);
            }
            if (! ($body instanceof StreamInterface)) {
                $body = $this->streamFactory->createStream($body);
            }

            $request = $request->withBody($body);
        }

        foreach ($this->mergeHeaders($headers) as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    protected function getEndpoint(string $endpoint): string
    {
        if (strpos($endpoint, 'http://') === 0 || strpos($endpoint, 'https://') === 0) {
            return $endpoint;
        }

        return $this->baseUri.$endpoint;
    }

    protected function mergeHeaders(array $headers): array
    {
        return array_merge($this->defaultHeaders, $headers);
    }
}
