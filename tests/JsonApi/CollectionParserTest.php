<?php

namespace Swis\JsonApi\Client\Tests\JsonApi;

use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\JsonApi\CollectionParser;
use Swis\JsonApi\Client\JsonApi\ItemParser;
use Swis\JsonApi\Client\Tests\AbstractTest;
use Swis\JsonApi\Client\Tests\Mocks\Items\PlainItem;

class CollectionParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_art4resourcecollection_to_collection()
    {
        $itemParser = $this->createMock(ItemParser::class);
        $itemParser->expects($this->exactly(2))
            ->method('parse')
            ->willReturn(new PlainItem());

        $parser = new CollectionParser($itemParser);
        $collection = $parser->parse($this->getArt4ResourceCollection());

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(PlainItem::class, $collection->get(0));
        $this->assertInstanceOf(PlainItem::class, $collection->get(1));
    }

    /**
     * @return \Art4\JsonApiClient\ResourceCollection
     */
    protected function getArt4ResourceCollection()
    {
        $collection = [
            'data' => [
                [
                    'id'         => '1',
                    'type'       => 'plain',
                    'attributes' => [
                        'foo' => 'bar',
                    ],
                ],
                [
                    'id'         => '2',
                    'type'       => 'plain',
                    'attributes' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($collection));

        return $jsonApiItem->get('data');
    }
}
