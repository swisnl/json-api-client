<?php

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemDocument;

class DocumentFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itMakesAnItemdocumentForAnItem()
    {
        $item = (new Item(['foo' => 'bar']))->setId('123');

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertInstanceOf(ItemDocument::class, $document);
        static::assertSame($item, $document->getData());
    }

    /**
     * @test
     */
    public function itMakesACollectiondocumentForACollection()
    {
        $item = (new Item(['foo' => 'bar']))->setId('123');
        $collection = new Collection([$item]);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($collection);

        static::assertInstanceOf(CollectionDocument::class, $document);
        static::assertSame($collection, $document->getData());
    }

    /**
     * @test
     */
    public function itAddsIncludedToTheDocumentForAnItem()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itAddsIncludedToTheDocumentForACollection()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);
        $collection = new Collection([$item]);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($collection);

        static::assertContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itAddsIncludedToTheDocumentForSingularRelations()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itAddsIncludedToTheDocumentForPluralRelations()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('456');
        $childTwoItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('789');
        $item->setRelation('child', new Collection([$childItem, $childTwoItem]));

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertContains($childItem, $document->getIncluded());
        static::assertContains($childTwoItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itDoesNotAddIncludedToTheDocumentIfItHasNoType()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setId('456');
        $item->setRelation('child', $childItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertNotContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itDoesNotAddIncludedToTheDocumentIfItHasNoId()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar');
        $item->setRelation('child', $childItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertNotContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itDoesNotAddIncludedToTheDocumentIfItHasNoAttributesOrRelationships()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item())->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertNotContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itAddsIncludedToTheDocumentIfItHasATypeIdAndAttributesButNoRelationships()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itAddsIncludedToTheDocumentIfItHasATypeIdAndRelationshipsButNoAttributes()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item())->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);
        $anotherChildItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('789');
        $childItem->setRelation('child', $anotherChildItem);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertContains($childItem, $document->getIncluded());
    }

    /**
     * @test
     */
    public function itDoesNotAddIncludedToTheDocumentIfTheRelationshipShouldBeOmitted()
    {
        $item = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('123');
        $childItem = (new Item(['foo' => 'bar']))->setType('foo-bar')->setId('456');
        $item->setRelation('child', $childItem);
        $item->getRelation('child')->setOmitIncluded(true);

        $documentFactory = new DocumentFactory();

        $document = $documentFactory->make($item);

        static::assertNotContains($childItem, $document->getIncluded());
    }
}
