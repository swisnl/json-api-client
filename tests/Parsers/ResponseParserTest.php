<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\InvalidResponseDocument;
use Swis\JsonApi\Client\Parsers\DocumentParser;
use Swis\JsonApi\Client\Parsers\ResponseParser;

class ResponseParserTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateAnInstanceUsingAFactoryMethod()
    {
        $this->assertInstanceOf(ResponseParser::class, ResponseParser::create());
    }

    /**
     * @test
     */
    public function itConvertsPsrReponseToDocument()
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
    public function itParsesAResponseWithAnEmptyBody()
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
    public function itParsesAnErrorResponse()
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
