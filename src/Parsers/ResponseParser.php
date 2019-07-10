<?php

namespace Swis\JsonApi\Client\Parsers;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\InvalidResponseDocument;

class ResponseParser implements ResponseParserInterface
{
    /**
     * @var \Swis\JsonApi\Client\Interfaces\DocumentParserInterface
     */
    private $parser;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DocumentParserInterface $parser
     */
    public function __construct(DocumentParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function parse(ResponseInterface $response): DocumentInterface
    {
        $document = new InvalidResponseDocument();

        if ($this->responseHasBody($response)) {
            $document = $this->parser->parse((string)$response->getBody());
        } elseif ($this->responseHasSuccessfulStatusCode($response)) {
            $document = new Document();
        }

        $document->setResponse($response);

        return $document;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    private function responseHasBody(ResponseInterface $response): bool
    {
        return (bool)$response->getBody()->getSize();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return bool
     */
    private function responseHasSuccessfulStatusCode(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }
}
