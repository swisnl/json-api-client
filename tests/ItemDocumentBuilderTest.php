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

        $itemDocument = $itemDocumentBuilder->build(new Item(), $data, '123');

        $item = $itemDocument->getData();
        static::assertInstanceOf(Item::class, $item);
        static::assertEquals($item->getId(), '123');
        static::assertEquals($item->key1, $data['key1']);
        static::assertEquals($item->key2, $data['key2']);
    }

    /**
     * @test
     */
    public function it_fills_items_without_id()
    {
        $typeMapper = new TypeMapper();
        $itemHydrator = new ItemHydrator($typeMapper);
        $itemDocumentBuilder = new ItemDocumentBuilder($itemHydrator);

        $itemDocument = $itemDocumentBuilder->build(new Item(), []);

        $item = $itemDocument->getData();
        static::assertInstanceOf(Item::class, $item);
        static::assertNull($item->getId());
    }

    /**
     * @test
     * @dataProvider provideIdArguments
     *
     * @param $givenId
     * @param $expectedId
     */
    public function it_fills_the_id_when_not_null_or_empty_string($givenId, $expectedId)
    {
        $typeMapper = new TypeMapper();
        $itemHydrator = new ItemHydrator($typeMapper);
        $itemDocumentBuilder = new ItemDocumentBuilder($itemHydrator);

        $itemDocument = $itemDocumentBuilder->build(new Item(), [], $givenId);

        $item = $itemDocument->getData();
        static::assertInstanceOf(Item::class, $item);
        static::assertSame($item->getId(), $expectedId);
    }

    public function provideIdArguments(): array
    {
        return [
            ['0', '0'],
            ['12', '12'],
            ['foo-bar', 'foo-bar'],
            [null, null],
            ['', null],
        ];
    }
}
