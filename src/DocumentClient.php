<?php

namespace Swis\JsonApi\Client;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Interfaces\ClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\ItemDocumentInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;

class DocumentClient implements DocumentClientInterface
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\ClientInterface
     */
    private $client;

    /**
     * @var \Swis\JsonApi\Client\Interfaces\ResponseParserInterface
     */
    private $parser;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ClientInterface         $client
     * @param \Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser
     */
    public function __construct(ClientInterface $client, ResponseParserInterface $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->client->getBaseUri();
    }

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri): void
    {
        $this->client->setBaseUri($baseUri);
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function get(string $endpoint, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->get($endpoint, $headers));
    }

    /**
     * @param string                                                $endpoint
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $body
     * @param array                                                 $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function post(string $endpoint, ItemDocumentInterface $body, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->post($endpoint, $this->prepareBody($body), $headers));
    }

    /**
     * @param string                                                $endpoint
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $body
     * @param array                                                 $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function patch(string $endpoint, ItemDocumentInterface $body, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->patch($endpoint, $this->prepareBody($body), $headers));
    }

    /**
     * @param string $endpoint
     * @param array  $headers
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $endpoint, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->delete($endpoint, $headers));
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $body
     *
     * @return string
     */
    protected function prepareBody(ItemDocumentInterface $body): string
    {
        return $this->sanitizeJson(json_encode($body));
    }

    /**
     * @param string $json
     *
     * @return string
     */
    protected function sanitizeJson(string $json): string
    {
        return str_replace('\r\n', '\\n', $json);
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    protected function parseResponse(ResponseInterface $response): DocumentInterface
    {
        return $this->parser->parse($response);
    }
}
