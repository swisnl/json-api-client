<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Document;
use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Interfaces\DocumentClientInterface;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemDocument;
use Swis\JsonApi\Client\Tests\Mocks\MockRepository;

class RepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_the_client()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);
        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($client, $repository->getClient());
    }

    /**
     * @test
     */
    public function it_can_get_the_endpoint()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);
        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame('mocks', $repository->getEndpoint());
    }

    /**
     * @test
     */
    public function it_can_get_all()
    {
        $document = new Document;

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);

        $client->expects($this->once())
            ->method('get')
            ->with('mocks?foo=bar', ['Test-Header' => 'Foo-Bar'])
            ->willReturn($document);

        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($document, $repository->all(['foo' => 'bar'], ['Test-Header' => 'Foo-Bar']));
    }

    /**
     * @test
     */
    public function it_can_take_one()
    {
        $document = new Document;

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);

        $client->expects($this->once())
            ->method('get')
            ->with('mocks?foo=bar', ['Test-Header' => 'Foo-Bar'])
            ->willReturn($document);

        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($document, $repository->take(['foo' => 'bar'], ['Test-Header' => 'Foo-Bar']));
    }

    /**
     * @test
     */
    public function it_can_find_one()
    {
        $document = new Document;

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);

        $client->expects($this->once())
            ->method('get')
            ->with('mocks/1?foo=bar', ['Test-Header' => 'Foo-Bar'])
            ->willReturn($document);

        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($document, $repository->find('1', ['foo' => 'bar'], ['Test-Header' => 'Foo-Bar']));
    }

    /**
     * @test
     */
    public function it_can_save_new()
    {
        $document = new ItemDocument;
        $document->setData(new Item);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);

        $client->expects($this->once())
            ->method('post')
            ->with('mocks?foo=bar', $document, ['Test-Header' => 'Foo-Bar'])
            ->willReturn($document);

        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($document, $repository->save(new Item, ['foo' => 'bar'], ['Test-Header' => 'Foo-Bar']));
    }

    /**
     * @test
     */
    public function it_can_save_existing()
    {
        $document = new ItemDocument;
        $document->setData((new Item)->setId('1'));

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);

        $client->expects($this->once())
            ->method('patch')
            ->with('mocks/1?foo=bar', $document, ['Test-Header' => 'Foo-Bar'])
            ->willReturn($document);

        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($document, $repository->save((new Item)->setId('1'), ['foo' => 'bar'], ['Test-Header' => 'Foo-Bar']));
    }

    /**
     * @test
     */
    public function it_can_delete()
    {
        $document = new Document;

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Swis\JsonApi\Client\Interfaces\DocumentClientInterface $client */
        $client = $this->createMock(DocumentClientInterface::class);

        $client->expects($this->once())
            ->method('delete')
            ->with('mocks/1?foo=bar', ['Test-Header' => 'Foo-Bar'])
            ->willReturn($document);

        $repository = new MockRepository($client, new DocumentFactory);

        $this->assertSame($document, $repository->delete('1', ['foo' => 'bar'], ['Test-Header' => 'Foo-Bar']));
    }
}
