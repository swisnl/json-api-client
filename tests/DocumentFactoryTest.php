<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\CollectionDocument;
use Swis\JsonApi\Client\DocumentFactory;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemDocument;

class DocumentFactoryTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_makes_an_itemdocument_for_an_item()
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
    public function it_makes_a_collectiondocument_for_a_collection()
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
    public function it_adds_included_to_the_document_for_an_item()
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
    public function it_adds_included_to_the_document_for_a_collection()
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
    public function it_adds_included_to_the_document_for_singular_relations()
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
    public function it_adds_included_to_the_document_for_plural_relations()
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
    public function it_does_not_add_included_to_the_document_if_it_has_no_type()
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
    public function it_does_not_add_included_to_the_document_if_it_has_no_id()
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
    public function it_does_not_add_included_to_the_document_if_it_has_no_attributes_or_relationships()
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
    public function it_adds_included_to_the_document_if_it_has_a_type_id_and_attributes_but_no_relationships()
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
    public function it_adds_included_to_the_document_if_it_has_a_type_id_and_relationships_but_no_attributes()
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
    public function it_does_not_add_included_to_the_document_if_the_relationship_should_be_omitted()
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
