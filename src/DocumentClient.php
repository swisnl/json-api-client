<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Interfaces\ClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\ItemDocumentInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\Parsers\ResponseParser;

class DocumentClient implements DocumentClientInterface
{
    private ClientInterface $client;

    private ResponseParserInterface $parser;

    public function __construct(ClientInterface $client, ResponseParserInterface $parser)
    {
        $this->client = $client;
        $this->parser = $parser;
    }

    /**
     * @return static
     */
    public static function create(?TypeMapperInterface $typeMapper = null, ?HttpClientInterface $client = null): self
    {
        return new static(new Client($client), ResponseParser::create($typeMapper));
    }

    public function getBaseUri(): string
    {
        return $this->client->getBaseUri();
    }

    public function setBaseUri(string $baseUri): void
    {
        $this->client->setBaseUri($baseUri);
    }

    public function get(string $endpoint, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->get($endpoint, $headers));
    }

    public function post(string $endpoint, ItemDocumentInterface $body, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->post($endpoint, $this->prepareBody($body), $headers));
    }

    public function patch(string $endpoint, ItemDocumentInterface $body, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->patch($endpoint, $this->prepareBody($body), $headers));
    }

    public function delete(string $endpoint, array $headers = []): DocumentInterface
    {
        return $this->parseResponse($this->client->delete($endpoint, $headers));
    }

    protected function prepareBody(ItemDocumentInterface $body): string
    {
        return $this->sanitizeJson(json_encode($body, JSON_THROW_ON_ERROR));
    }

    protected function sanitizeJson(string $json): string
    {
        return str_replace('\r\n', '\\n', $json);
    }

    protected function parseResponse(ResponseInterface $response): DocumentInterface
    {
        return $this->parser->parse($response);
    }
}
