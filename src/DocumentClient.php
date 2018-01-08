<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\ClientInterface;
use Swis\JsonApi\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Interfaces\DocumentInterface;
use Swis\JsonApi\Interfaces\ItemDocumentInterface;
use Swis\JsonApi\Interfaces\ParserInterface;
use Swis\JsonApi\Interfaces\ResponseInterface;

class DocumentClient implements DocumentClientInterface
{
    /**
     * @var \Swis\JsonApi\Interfaces\ClientInterface
     */
    private $client;

    /**
     * @var \Swis\JsonApi\ItemDocumentSerializer
     */
    private $itemDocumentSerializer;

    /**
     * @var \Swis\JsonApi\Interfaces\ParserInterface
     */
    private $parser;

    /**
     * @param \Swis\JsonApi\Interfaces\ClientInterface $client
     * @param \Swis\JsonApi\ItemDocumentSerializer     $itemDocumentSerializer
     * @param \Swis\JsonApi\Interfaces\ParserInterface $parser
     */
    public function __construct(
        ClientInterface $client,
        ItemDocumentSerializer $itemDocumentSerializer,
        ParserInterface $parser
    ) {
        $this->client = $client;
        $this->itemDocumentSerializer = $itemDocumentSerializer;
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
    public function setBaseUri(string $baseUri)
    {
        $this->client->setBaseUri($baseUri);
    }

    /**
     * @param string $endpoint
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function get(string $endpoint): DocumentInterface
    {
        return $this->parseResponse($this->client->get($endpoint));
    }

    /**
     * @param string                                         $endpoint
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $body
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function post(string $endpoint, ItemDocumentInterface $body): DocumentInterface
    {
        return $this->parseResponse($this->client->post($endpoint, $this->prepareBody($body)));
    }

    /**
     * @param string                                         $endpoint
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $body
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function patch(string $endpoint, ItemDocumentInterface $body): DocumentInterface
    {
        return $this->parseResponse($this->client->patch($endpoint, $this->prepareBody($body)));
    }

    /**
     * @param string $endpoint
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function delete(string $endpoint): DocumentInterface
    {
        return $this->parseResponse($this->client->delete($endpoint));
    }

    /**
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $body
     *
     * @return string
     */
    protected function prepareBody(ItemDocumentInterface $body): string
    {
        return $this->sanitizeJson($this->itemDocumentSerializer->serialize($body));
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
     * @param \Swis\JsonApi\Interfaces\ResponseInterface $response
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    protected function parseResponse(ResponseInterface $response): DocumentInterface
    {
        if ($response->hasBody()) {
            return $this->parser->deserialize($response->getBody());
        }

        if ($response->hasSuccessfulStatusCode()) {
            return new Document();
        }

        return new InvalidResponseDocument();
    }
}
