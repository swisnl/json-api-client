<?php

class ItemDocumentBuilderTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_fills_items_from_id_and_attributes()
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];

        $typeMapper = new \Swis\JsonApi\TypeMapper();
        $itemHydrator = new \Swis\JsonApi\ItemHydrator($typeMapper);
        $itemDocumentBuilder = new \Swis\JsonApi\ItemDocumentBuilder($itemHydrator);

        $itemDocument = $itemDocumentBuilder->build(new \Swis\JsonApi\Items\JenssegersItem(), $data, 123);

        $item = $itemDocument->getData();
        static::assertInstanceOf(\Swis\JsonApi\Items\JenssegersItem::class, $item);
        static::assertEquals($item->key1, $data['key1']);
        static::assertEquals($item->key2, $data['key2']);
    }
}
