<?php

namespace Swis\JsonApi\Client\Tests;

use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\DocumentClient;
use Swis\JsonApi\Client\Interfaces\ClientInterface;
use Swis\JsonApi\Client\Interfaces\ResponseParserInterface;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemDocument;

class DocumentClientTest extends AbstractTest
{
    /**
     * @test
     */
    public function the_base_url_can_be_changed_after_instantiation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $client->expects($this->once())
            ->method('getBaseUri')
            ->willReturn('http://www.test.com');

        $client->expects($this->once())
            ->method('setBaseUri')
            ->with('http://www.test-changed.com');

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->getMockBuilder(ResponseParserInterface::class)->getMock();

        $documentClient = new DocumentClient($client, $parser);

        $this->assertEquals('http://www.test.com', $documentClient->getBaseUri());
        $documentClient->setBaseUri('http://www.test-changed.com');
    }

    /**
     * @test
     */
    public function it_builds_a_get_request()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $client->expects($this->once())
            ->method('get')
            ->with('/test/1', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->getMockBuilder(ResponseParserInterface::class)->getMock();

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
    public function it_builds_a_delete_request()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $client->expects($this->once())
            ->method('delete')
            ->with('/test/1', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->getMockBuilder(ResponseParserInterface::class)->getMock();

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
    public function it_builds_a_patch_request()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();
        $itemDocument = new ItemDocument();
        $itemDocument->setData((new Item())->setType('test')->setId('1'));

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $client->expects($this->once())
            ->method('patch')
            ->with('/test/1', '{"data":{"type":"test","id":"1"}}', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->getMockBuilder(ResponseParserInterface::class)->getMock();

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
    public function it_builds_a_post_request()
    {
        $response = $this->createMock(ResponseInterface::class);
        $document = new Document();
        $itemDocument = new ItemDocument();
        $itemDocument->setData((new Item())->setType('test'));

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)->getMock();

        $client->expects($this->once())
            ->method('post')
            ->with('/test/1', '{"data":{"type":"test"}}', ['X-Foo' => 'bar'])
            ->willReturn($response);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\ResponseParserInterface $parser */
        $parser = $this->getMockBuilder(ResponseParserInterface::class)->getMock();

        $parser->expects($this->once())
            ->method('parse')
            ->with($response)
            ->willReturn($document);

        $documentClient = new DocumentClient($client, $parser);

        $responseDocument = $documentClient->post('/test/1', $itemDocument, ['X-Foo' => 'bar']);

        $this->assertSame($document, $responseDocument);
    }
}
