<?php

namespace Swis\JsonApi\Client\Tests\JsonApi;

use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\TypeMapperInterface;
use Swis\JsonApi\Client\JsonApi\ItemParser;
use Swis\JsonApi\Client\JsonApi\LinksParser;
use Swis\JsonApi\Client\JsonApi\MetaParser;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Tests\AbstractTest;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\PlainItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithoutRelationshipsItem;
use Swis\JsonApi\Client\TypeMapper;

class ItemParserTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_converts_art4resource_to_item()
    {
        $parser = $this->getItemParser();
        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(ItemInterface::class, $item);

        static::assertEquals('master', $item->getType());
        static::assertEquals('1', $item->getId());
        static::assertEquals(['description' => 'test', 'active' => true], $item->getAttributes());
        static::assertInstanceOf(Links::class, $item->getLinks());
        static::assertInstanceOf(Meta::class, $item->getMeta());
    }

    /**
     * @test
     */
    public function it_uses_the_correct_item_from_typemapping()
    {
        $parser = $this->getItemParser();

        foreach (range(0, 10) as $i) {
            $type = $this->faker->slug;
            $id = $this->faker->randomDigit;

            $item = $parser->parse($this->getJsonApiItemMock($type, $id));

            static::assertInstanceOf(PlainItem::class, $item);
            static::assertEquals($type, $item->getType());
            static::assertEquals($id, $item->getId());
        }
    }

    /**
     * @test
     */
    public function it_parses_a_has_one_relationship()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $parser = $this->getItemParser($typeMapper);

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(HasOneRelation::class, $item->getRelation('child'));
        static::assertInstanceOf(Links::class, $item->getRelation('child')->getLinks());
        static::assertInstanceOf(Meta::class, $item->getRelation('child')->getMeta());

        static::assertInstanceOf(ChildItem::class, $item->getRelation('child')->getIncluded());
        static::assertEquals('child', $item->getRelation('child')->getIncluded()->getType());
        static::assertEquals('2', $item->getRelation('child')->getIncluded()->getId());
    }

    /**
     * @test
     */
    public function it_parses_a_has_many_relationship()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $parser = $this->getItemParser($typeMapper);

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(HasManyRelation::class, $item->getRelation('children'));
        static::assertInstanceOf(Links::class, $item->getRelation('children')->getLinks());
        static::assertInstanceOf(Meta::class, $item->getRelation('children')->getMeta());

        static::assertInstanceOf(Collection::class, $item->getRelation('children')->getIncluded());
        static::assertCount(2, $item->getRelation('children')->getIncluded());
        static::assertEquals('child', $item->getRelation('children')->getIncluded()->get(0)->getType());
        static::assertEquals('3', $item->getRelation('children')->getIncluded()->get(0)->getId());
        static::assertEquals('child', $item->getRelation('children')->getIncluded()->get(1)->getType());
        static::assertEquals('4', $item->getRelation('children')->getIncluded()->get(1)->getId());
    }

    /**
     * @test
     */
    public function it_parses_a_morph_to_relation()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $parser = $this->getItemParser($typeMapper);

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(MorphToRelation::class, $item->getRelation('morph'));
        static::assertInstanceOf(Links::class, $item->getRelation('morph')->getLinks());
        static::assertInstanceOf(Meta::class, $item->getRelation('morph')->getMeta());

        static::assertInstanceOf(ItemInterface::class, $item->getRelation('morph')->getIncluded());
        static::assertEquals('child', $item->getRelation('morph')->getIncluded()->getType());
        static::assertEquals('5', $item->getRelation('morph')->getIncluded()->getId());
    }

    /**
     * @test
     */
    public function it_parses_a_morph_to_many_relation()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $parser = $this->getItemParser($typeMapper);

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(MorphToManyRelation::class, $item->getRelation('morphmany'));
        static::assertInstanceOf(Links::class, $item->getRelation('morphmany')->getLinks());
        static::assertInstanceOf(Meta::class, $item->getRelation('morphmany')->getMeta());

        static::assertInstanceOf(Collection::class, $item->getRelation('morphmany')->getIncluded());
        static::assertCount(3, $item->getRelation('morphmany')->getIncluded());
        static::assertEquals('child', $item->getRelation('morphmany')->getIncluded()->get(0)->getType());
        static::assertEquals('6', $item->getRelation('morphmany')->getIncluded()->get(0)->getId());
        static::assertEquals('child', $item->getRelation('morphmany')->getIncluded()->get(1)->getType());
        static::assertEquals('7', $item->getRelation('morphmany')->getIncluded()->get(1)->getId());
        static::assertEquals('child', $item->getRelation('morphmany')->getIncluded()->get(2)->getType());
        static::assertEquals('8', $item->getRelation('morphmany')->getIncluded()->get(2)->getId());
    }

    /**
     * @test
     */
    public function it_parses_an_unknown_singular_relation_as_morph_to()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item-without-relationships', WithoutRelationshipsItem::class);
        $parser = $this->getItemParser($typeMapper);

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(MorphToRelation::class, $item->getRelation('morph'));
        static::assertInstanceOf(Links::class, $item->getRelation('morph')->getLinks());
        static::assertInstanceOf(Meta::class, $item->getRelation('morph')->getMeta());

        static::assertInstanceOf(ItemInterface::class, $item->getRelation('morph')->getIncluded());
    }

    /**
     * @test
     */
    public function it_parses_an_unknown_plural_relation_as_morph_to_many()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item-without-relationships', WithoutRelationshipsItem::class);
        $parser = $this->getItemParser($typeMapper);

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(MorphToManyRelation::class, $item->getRelation('morphmany'));
        static::assertInstanceOf(Links::class, $item->getRelation('morphmany')->getLinks());
        static::assertInstanceOf(Meta::class, $item->getRelation('morphmany')->getMeta());

        static::assertInstanceOf(Collection::class, $item->getRelation('morphmany')->getIncluded());
        static::assertCount(3, $item->getRelation('morphmany')->getIncluded());
    }

    /**
     * @test
     */
    public function it_parses_links()
    {
        $parser = $this->getItemParser();

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(Links::class, $item->getLinks());

        static::assertEquals(new Links(['self' => new Link('http://example.com/master/1')]), $item->getLinks());
    }

    /**
     * @test
     */
    public function it_parses_meta()
    {
        $parser = $this->getItemParser();

        $item = $parser->parse($this->getJsonApiItemMock('master', '1'));

        static::assertInstanceOf(Meta::class, $item->getMeta());

        static::assertEquals(new Meta(['foo' => 'bar']), $item->getMeta());
    }

    /**
     * @param \Swis\JsonApi\Client\Interfaces\TypeMapperInterface|null $typeMapper
     *
     * @return \Swis\JsonApi\Client\JsonApi\ItemParser
     */
    private function getItemParser(TypeMapperInterface $typeMapper = null): ItemParser
    {
        return new ItemParser(
            $typeMapper ?? $this->getTypeMapperMock(),
            new LinksParser(new MetaParser()),
            new MetaParser()
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Interfaces\TypeMapperInterface
     */
    private function getTypeMapperMock()
    {
        $typeMapper = $this->createMock(TypeMapper::class);
        $typeMapper->method('hasMapping')
            ->willReturn(true);

        $typeMapper->method('getMapping')
            ->willReturnCallback(
                static function (string $type) {
                    return (new PlainItem())->setType($type);
                }
            );

        return $typeMapper;
    }

    /**
     * @param $type
     * @param $id
     *
     * @return mixed
     */
    private function getJsonApiItemMock($type, $id)
    {
        $data = [
            'data'     => [
                'type'          => $type,
                'id'            => $id,
                'attributes'    => [
                    'description' => 'test',
                    'active'      => true,
                ],
                'relationships' => [
                    'child'     => [
                        'data'  => [
                            'type' => 'child',
                            'id'   => '2',
                        ],
                        'links' => [
                            'self' => 'http://example.com/'.$type.'/'.$id.'/relationships/child',
                        ],
                        'meta'  => [
                            'foo' => 'bar',
                        ],
                    ],
                    'children'  => [
                        'data'  => [
                            [
                                'type' => 'child',
                                'id'   => '3',
                            ],
                            [
                                'type' => 'child',
                                'id'   => '4',
                            ],
                        ],
                        'links' => [
                            'self' => 'http://example.com/'.$type.'/'.$id.'/relationships/children',
                        ],
                        'meta'  => [
                            'foo' => 'bar',
                        ],
                    ],
                    'morph'     => [
                        'data'  => [
                            'type' => 'child',
                            'id'   => '5',
                        ],
                        'links' => [
                            'self' => 'http://example.com/'.$type.'/'.$id.'/relationships/morph',
                        ],
                        'meta'  => [
                            'foo' => 'bar',
                        ],
                    ],
                    'morphmany' => [
                        'data'  => [
                            [
                                'type' => 'child',
                                'id'   => '6',
                            ],
                            [
                                'type' => 'child',
                                'id'   => '7',
                            ],
                            [
                                'type' => 'child',
                                'id'   => '8',
                            ],
                        ],
                        'links' => [
                            'self' => 'http://example.com/'.$type.'/'.$id.'/relationships/morphmany',
                        ],
                        'meta'  => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
                'links'         => [
                    'self' => 'http://example.com/master/1',
                ],
                'meta'          => [
                    'foo' => 'bar',
                ],
            ],
            'included' => [
                [
                    'type'       => 'child',
                    'id'         => '2',
                    'attributes' => [
                        'description' => 'test',
                        'active'      => true,
                    ],
                ],
                [
                    'type'       => 'child',
                    'id'         => '3',
                    'attributes' => [
                        'description' => 'test3',
                        'active'      => true,
                    ],
                ],
                [
                    'type'       => 'child',
                    'id'         => '4',
                    'attributes' => [
                        'description' => 'test4',
                        'active'      => true,
                    ],
                ],
                [
                    'type'       => 'child',
                    'id'         => '5',
                    'attributes' => [
                        'description' => 'test5',
                        'active'      => true,
                    ],
                ],
                [
                    'type'       => 'child',
                    'id'         => '6',
                    'attributes' => [
                        'description' => 'test6',
                        'active'      => true,
                    ],
                ],
                [
                    'type'       => 'child',
                    'id'         => '7',
                    'attributes' => [
                        'description' => 'test7',
                        'active'      => true,
                    ],
                ],
                [
                    'type'       => 'child',
                    'id'         => '8',
                    'attributes' => [
                        'description' => 'test8',
                        'active'      => true,
                    ],
                ],
            ],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($data));

        return $jsonApiItem->get('data');
    }
}
