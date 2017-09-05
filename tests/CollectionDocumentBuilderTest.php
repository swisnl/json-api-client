<?php

class CollectionDocumentBuilderTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_fills_items_from_array()
    {
        $collectionDocumentBuilder = new \Swis\JsonApi\CollectionDocumentBuilder();

        $data = [
            (new \Swis\JsonApi\Items\JenssegersItem(['key1' => 'value1']))->setId(1),
            (new \Swis\JsonApi\Items\JenssegersItem(['key2' => 'value2']))->setId(2),
        ];

        $collectionDocument = $collectionDocumentBuilder->build($data);

        /** @var \Swis\JsonApi\Collection $items */
        $items = $collectionDocument->getData();
        static::assertInstanceOf(\Swis\JsonApi\Collection::class, $items);

        static::assertInstanceOf(\Swis\JsonApi\Items\JenssegersItem::class, $items[0]);
        static::assertEquals(1, $items[0]->getId());
        static::assertEquals($data[0]['key1'], $items[0]->key1);

        static::assertInstanceOf(\Swis\JsonApi\Items\JenssegersItem::class, $items[1]);
        static::assertEquals(2, $items[1]->getId());
        static::assertEquals($data[1]['key2'], $items[1]->key2);
    }
}
