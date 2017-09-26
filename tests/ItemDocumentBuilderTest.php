<?php

namespace Swis\JsonApi\Tests;

use Swis\JsonApi\ItemDocumentBuilder;
use Swis\JsonApi\ItemHydrator;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\TypeMapper;

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

        $itemDocument = $itemDocumentBuilder->build(new JenssegersItem(), $data, 123);

        $item = $itemDocument->getData();
        static::assertInstanceOf(JenssegersItem::class, $item);
        static::assertEquals($item->key1, $data['key1']);
        static::assertEquals($item->key2, $data['key2']);
    }
}
