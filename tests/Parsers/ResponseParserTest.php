<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use GuzzleHttp\Psr7\Response;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\InvalidResponseDocument;
use Swis\JsonApi\Client\Parsers\DocumentParser;
use Swis\JsonApi\Client\Parsers\ResponseParser;
use Swis\JsonApi\Client\Tests\AbstractTest;

class ResponseParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_psr_reponse_to_document()
    {
        $documentParser = $this->createMock(DocumentParser::class);
        $documentParser->expects($this->once())
            ->method('parse')
            ->willReturn(new CollectionDocument());

        $parser = new ResponseParser($documentParser);

        $response = new Response(200, [], json_encode(['data' => []]));
        $document = $parser->parse($response);

        $this->assertInstanceOf(CollectionDocument::class, $document);
        $this->assertSame($response, $document->getResponse());
    }

    /**
     * @test
     */
    public function it_parses_a_response_with_an_empty_body()
    {
        $documentParser = $this->createMock(DocumentParser::class);
        $documentParser->expects($this->never())
            ->method('parse');

        $parser = new ResponseParser($documentParser);

        $response = new Response(201);
        $document = $parser->parse($response);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame($response, $document->getResponse());
    }

    /**
     * @test
     */
    public function it_parses_an_error_response()
    {
        $documentParser = $this->createMock(DocumentParser::class);
        $documentParser->expects($this->never())
            ->method('parse');

        $parser = new ResponseParser($documentParser);

        $response = new Response(500);
        $document = $parser->parse($response);

        $this->assertInstanceOf(InvalidResponseDocument::class, $document);
        $this->assertSame($response, $document->getResponse());
    }
}
