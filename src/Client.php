<?php

namespace Swis\JsonApi;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Swis\JsonApi\Interfaces\ClientInterface;

class Client implements ClientInterface
{
    /**
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * @var string
     */
    const METHOD_PATCH = 'PATCH';

    /**
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var \Swis\JsonApi\RequestFactory
     */
    private $requestFactory;

    /**
     * @var \Swis\JsonApi\ResponseFactory
     */
    private $responseFactory;

    /**
     * @param \GuzzleHttp\ClientInterface   $client
     * @param string                        $baseUri
     * @param \Swis\JsonApi\RequestFactory  $requestFactory
     * @param \Swis\JsonApi\ResponseFactory $responseFactory
     */
    public function __construct(
        GuzzleClientInterface $client,
        string $baseUri,
        RequestFactory $requestFactory,
        ResponseFactory $responseFactory
    ) {
        $this->client = $client;
        $this->baseUri = $baseUri;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function get(string $endpoint, array $headers = [])
    {
        return $this->request(static::METHOD_GET, $endpoint, null, $headers);
    }

    /**
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function post(string $endpoint, $body, array $headers = [])
    {
        return $this->request(static::METHOD_POST, $endpoint, $body, $headers);
    }

    /**
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function patch(string $endpoint, $body, array $headers = [])
    {
        return $this->request(static::METHOD_PATCH, $endpoint, $body, $headers);
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function delete(string $endpoint, array $headers = [])
    {
        return $this->request(static::METHOD_DELETE, $endpoint, null, $headers);
    }

    /**
     * @param string                                                                         $method
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\ResponseInterface
     */
    public function request(string $method, string $endpoint, $body = null, array $headers = [])
    {
        $request = $this->buildRequest($method, $endpoint, $body, $headers);

        try {
            $response = $this->responseFactory->make($this->client->send($request));
        } catch (ClientException $e) {
            $response = $this->responseFactory->make($e->getResponse());
        } catch (ServerException $e) {
            $response = $this->responseFactory->make($e->getResponse());
        }

        return $response;
    }

    /**
     * @param string                                                                         $method
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function buildRequest(string $method, string $endpoint, $body = null, array $headers = []): RequestInterface
    {
        return $this->requestFactory->make($method, $this->getEndpoint($endpoint), $body, $headers);
    }

    /**
     * @param string $endpoint
     *
     * @return string
     */
    protected function getEndpoint(string $endpoint): string
    {
        return $this->baseUri.$endpoint;
    }
}
