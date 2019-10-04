<?php

namespace Swis\JsonApi\Client\Tests\Parsers;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Parsers\CollectionParser;
use Swis\JsonApi\Client\Parsers\ItemParser;
use Swis\JsonApi\Client\Tests\AbstractTest;
use Swis\JsonApi\Client\Tests\Mocks\Items\PlainItem;

class CollectionParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_data_to_collection()
    {
        $itemParser = $this->createMock(ItemParser::class);
        $itemParser->expects($this->exactly(2))
            ->method('parse')
            ->willReturn(new PlainItem());

        $parser = new CollectionParser($itemParser);
        $collection = $parser->parse($this->getResourceCollection());

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(PlainItem::class, $collection->get(0));
        $this->assertInstanceOf(PlainItem::class, $collection->get(1));
    }

    /**
     * @test
     * @dataProvider provideInvalidData
     *
     * @param mixed $invalidData
     */
    public function it_throws_when_data_is_not_an_array($invalidData)
    {
        $parser = new CollectionParser($this->createMock(ItemParser::class));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(sprintf('ResourceCollection has to be an array, "%s" given.', gettype($invalidData)));

        $parser->parse($invalidData);
    }

    public function provideInvalidData(): array
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        return [
            [1],
            [1.5],
            [false],
            [null],
            ['foo'],
            [$object],
        ];
    }

    /**
     * @return \stdClass
     */
    protected function getResourceCollection()
    {
        $data = [
            [
                'id' => '1',
                'type' => 'plain',
                'attributes' => [
                    'foo' => 'bar',
                ],
            ],
            [
                'id' => '2',
                'type' => 'plain',
                'attributes' => [
                    'foo' => 'bar',
                ],
            ],
        ];

        return json_decode(json_encode($data), false);
    }
}
