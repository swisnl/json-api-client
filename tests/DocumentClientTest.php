<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\DocumentClient;
use Swis\JsonApi\Client\Interfaces\ClientInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemDocument;

class DocumentClientTest extends TestCase
{
    /**
     * @test
     */
    public function itCanCreateAnInstanceUsingAFactoryMethod()
    {
        $this->assertInstanceOf(DocumentClient::class, DocumentClient::create());
    }

    /**
     * @test
     */
    public function theBaseUrlCanBeChangedAfterInstantiation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('getBaseUri')
            ->willReturn('http://www.test.com');

        $client->expects($this->once())
            ->method('setBaseUri')
            ->with('http://www.test-changed.com');

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->createMock(ResponseParserInterface::class);

        $documentClient = new DocumentClient($client, $parser);

        $this->assertEquals('http://www.test.com', $documentClient->getBaseUri());
        $documentClient->setBaseUri('http://www.test-changed.com');
    }

    /**
     * @test
     */
    public function itBuildsAGetRequest()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('get')
            ->with('/test/1', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->createMock(ResponseParserInterface::class);

        $parser->expects($this->once())
            ->method('parse')
            ->with($response)
            ->willReturn($document);

        $documentClient = new DocumentClient($client, $parser);

        $responseDocument = $documentClient->get('/test/1', ['X-Foo' => 'bar']);

        $this->assertSame($document, $responseDocument);
    }

    /**
     * @test
     */
    public function itBuildsADeleteRequest()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('delete')
            ->with('/test/1', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->createMock(ResponseParserInterface::class);

        $parser->expects($this->once())
            ->method('parse')
            ->with($response)
            ->willReturn($document);

        $documentClient = new DocumentClient($client, $parser);

        $responseDocument = $documentClient->delete('/test/1', ['X-Foo' => 'bar']);

        $this->assertSame($document, $responseDocument);
    }

    /**
     * @test
     */
    public function itBuildsAPatchRequest()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();
        $itemDocument = new ItemDocument();
        $itemDocument->setData((new Item())->setType('test')->setId('1'));

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('patch')
            ->with('/test/1', '{"data":{"type":"test","id":"1"}}', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->createMock(ResponseParserInterface::class);

        $parser->expects($this->once())
            ->method('parse')
            ->with($response)
            ->willReturn($document);

        $documentClient = new DocumentClient($client, $parser);

        $responseDocument = $documentClient->patch('/test/1', $itemDocument, ['X-Foo' => 'bar']);

        $this->assertSame($document, $responseDocument);
    }

    /**
     * @test
     */
    public function itBuildsAPostRequest()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();
        $itemDocument = new ItemDocument();
        $itemDocument->setData((new Item())->setType('test'));

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('post')
            ->with('/test/1', '{"data":{"type":"test"}}', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->createMock(ResponseParserInterface::class);

        $parser->expects($this->once())
            ->method('parse')
            ->with($response)
            ->willReturn($document);

        $documentClient = new DocumentClient($client, $parser);

        $responseDocument = $documentClient->post('/test/1', $itemDocument, ['X-Foo' => 'bar']);

        $this->assertSame($document, $responseDocument);
    }
}
