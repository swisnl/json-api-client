<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemDocumentBuilder;
use Swis\JsonApi\Client\ItemHydrator;
use Swis\JsonApi\Client\TypeMapper;

class ItemDocumentBuilderTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_fills_items_from_id_and_attributes()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];

        $typeMapper = new TypeMapper();
        $itemHydrator = new ItemHydrator($typeMapper);
        $itemDocumentBuilder = new ItemDocumentBuilder($itemHydrator);

        $itemDocument = $itemDocumentBuilder->build(new Item(), $data, 123);

        $item = $itemDocument->getData();
        static::assertInstanceOf(Item::class, $item);
        static::assertEquals($item->key1, $data['key1']);
        static::assertEquals($item->key2, $data['key2']);
    }
}
