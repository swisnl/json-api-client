<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Parsers;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\Interfaces\DocumentInterface;
use Swis\JsonApi\Client\Interfaces\DocumentParserInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\InvalidResponseDocument;

class ResponseParser implements ResponseParserInterface
{
    private DocumentParserInterface $parser;

    public function __construct(DocumentParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return static
     */
    public static function create(?TypeMapperInterface $typeMapper = null): self
    {
        return new static(DocumentParser::create($typeMapper));
    }

    public function parse(ResponseInterface $response): DocumentInterface
    {
        $document = new InvalidResponseDocument;

        if ($this->responseHasBody($response)) {
            $document = $this->parser->parse((string) $response->getBody());
        } elseif ($this->responseHasSuccessfulStatusCode($response)) {
            $document = new Document;
        }

        $document->setResponse($response);

        return $document;
    }

    private function responseHasBody(ResponseInterface $response): bool
    {
        $body = $response->getBody();
        $size = $body->getSize();

        if ($size === 0) {
            return false;
        }
        if (is_int($size) && $size > 0) {
            return true;
        }

        $contents = (string) $body;
        $hasBody = trim($contents) !== '';

        if ($body->isSeekable()) {
            $body->rewind();
        }

        return $hasBody;
    }

    private function responseHasSuccessfulStatusCode(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }
}
