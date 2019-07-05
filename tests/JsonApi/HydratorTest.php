<?php

namespace Swis\JsonApi\Client\Tests\JsonApi;

use Art4\JsonApiClient\ResourceCollectionInterface as CollectionInterface;
use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\JsonApi\Hydrator;
use Swis\JsonApi\Client\JsonApi\LinksParser;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Tests\AbstractTest;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\PlainItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithoutRelationshipsItem;
use Swis\JsonApi\Client\TypeMapper;

class HydratorTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_hydrates_the_correct_item_from_mapping()
    {
        $hydrator = $this->getHydrator();

        foreach (range(0, 10) as $i) {
            $type = $this->faker->slug;
            $id = $this->faker->randomDigit;

            $item = $hydrator->hydrateItem($this->getJsonApiItemMock($type, $id));

            static::assertInstanceOf(PlainItem::class, $item);
            static::assertEquals($type, $item->getType());
            static::assertEquals($id, $item->getId());
        }
    }

    /**
     * @return \Swis\JsonApi\Client\JsonApi\Hydrator
     */
    protected function getHydrator(): Hydrator
    {
        return new Hydrator($this->getTypeMapperMock(), new LinksParser());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Swis\JsonApi\Client\Interfaces\TypeMapperInterface
     */
    protected function getTypeMapperMock()
    {
        $typeMapper = $this->createMock(TypeMapper::class);
        $typeMapper->method('hasMapping')->willReturn(true);
        $typeMapper->method('getMapping')->will(
            $this->returnCallback(
                function (string $type) {
                    return (new PlainItem())->setType($type);
                }
            )
        );

        return $typeMapper;
    }

    /**
     * @param $type
     * @param $id
     *
     * @return mixed
     */
    protected function getJsonApiItemMock($type, $id)
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
                        'data' => [
                            'type' => 'child',
                            'id'   => '2',
                        ],
                        'links' => [
                            'self' => 'http://example.com/'.$type.'/'.$id.'/relationships/child',
                        ],
                        'meta' => [
                            'foo' => 'bar',
                        ],
                    ],
                    'morph'     => [
                        'data' => [
                            'type' => 'child',
                            'id'   => '3',
                        ],
                        'meta' => [
                            'foo' => 'bar',
                        ],
                    ],
                    'morphmany' => [
                        'data' => [
                            [
                                'type' => 'child',
                                'id'   => '4',
                            ],
                            [
                                'type' => 'child',
                                'id'   => '5',
                            ],
                            [
                                'type' => 'child',
                                'id'   => '6',
                            ],
                        ],
                        'links' => [
                            'self' => 'http://example.com/'.$type.'/'.$id.'/relationships/morphmany',
                        ],
                    ],
                ],
                'links' => [
                    'self' => 'http://example.com/master/1',
                ],
                'meta' => [
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
            ],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($data));

        return $jsonApiItem->get('data');
    }

    /**
     * @param $id
     * @param $parentType
     * @param $parentId
     *
     * @return mixed
     */
    protected function getJsonApiChildItemMock($id, $parentType = null, $parentId = null)
    {
        $data = [
            'data'     => [
                'type'          => 'child',
                'id'            => $id,
                'attributes'    => [
                    'description' => 'test',
                    'active'      => true,
                ],
            ],
            'included' => [],
        ];

        if (null !== $parentType && null !== $parentId) {
            $data['data']['relationships'] = [
                'parent' => [
                    'data' => [
                        'type' => $parentType,
                        'id'   => $parentId,
                    ],
                ],
            ];
        }

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($data));

        return $jsonApiItem->get('data');
    }

    /**
     * @test
     */
    public function it_hydrates_relationships()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $hydrator = new Hydrator($typeMapper, new LinksParser());

        $childJsonApiItem = $this->getJsonApiChildItemMock(2, 'master', 1);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertEquals('master', $masterItem->getType());
        static::assertEquals(1, $masterItem->getId());

        static::assertInstanceOf(MasterItem::class, $masterItem);
        static::assertInstanceOf(HasOneRelation::class, $masterItem->getRelation('child'));
        static::assertInstanceOf(Links::class, $masterItem->getRelation('child')->getLinks());
        static::assertInstanceOf(Meta::class, $masterItem->getRelation('child')->getMeta());

        static::assertSame($childItem, $masterItem->getRelation('child')->getIncluded());
        static::assertEquals('child', $masterItem->getRelation('child')->getIncluded()->getType());
        static::assertEquals(2, $masterItem->getRelation('child')->getIncluded()->getId());
        static::assertSame($masterItem, $masterItem->getRelation('child')->getIncluded()->getRelation('parent')->getIncluded());
        static::assertSame('http://example.com/master/1/relationships/child', $masterItem->getRelation('child')->getLinks()->self->getHref());
        static::assertSame('bar', $masterItem->getRelation('child')->getMeta()->foo);
    }

    /**
     * @test
     */
    public function it_does_not_hydrate_relationships_without_data()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('master', MasterItem::class);
        $hydrator = new Hydrator($typeMapper, new LinksParser());

        $data = [
            'data' => [
                'type'          => 'master',
                'id'            => 1,
                'attributes'    => [
                    'description' => 'test',
                    'active'      => true,
                ],
                'relationships' => [
                    'child' => [
                        'links' => [
                            'self'    => 'http://example.com/master/1/relationships/child',
                            'related' => 'http://example.com/master/1/child',
                        ],
                    ],
                ],
            ],
        ];

        $manager = new Manager();
        $jsonApiItem = $manager->parse(json_encode($data));

        $masterJsonApiItem = $jsonApiItem->get('data');
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$masterJsonApiItem]),
            new Collection([$masterItem])
        );

        static::assertEquals('master', $masterItem->getType());
        static::assertEquals(1, $masterItem->getId());

        static::assertInstanceOf(MasterItem::class, $masterItem);
        static::assertFalse($masterItem->hasRelation('child'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Art4\JsonApiClient\ResourceCollectionInterface
     */
    protected function getJsonApiItemCollectionMock()
    {
        $jsonApiCollection = $this->createMock(CollectionInterface::class);

        $jsonApiCollection->method('asArray')
            ->willReturn(
                [
                    $this->getJsonApiItemMock('child', 2),
                    $this->getJsonApiItemMock('child', 3),
                    $this->getJsonApiItemMock('child', 4),
                ]
            );

        return $jsonApiCollection;
    }

    /**
     * @test
     */
    public function it_hydrates_a_collection_of_items_of_specific_types_without_includes()
    {
        $hydrator = $this->getHydrator();

        $collection = $hydrator->hydrateCollection($this->getJsonApiItemCollectionMock());

        static::assertInstanceOf(Collection::class, $collection);

        foreach ($collection as $item) {
            static::assertInstanceOf(Item::class, $item);
            static::assertNotEmpty($item->getType());
            static::assertNotEmpty($item->getId());
        }
    }

    /**
     * @test
     */
    public function it_handles_strings_as_ids()
    {
        $hydrator = $this->getHydrator();

        $type = $this->faker->slug;
        $id = $this->faker->slug;

        $item = $hydrator->hydrateItem($this->getJsonApiItemMock($type, $id));

        static::assertInstanceOf(Item::class, $item);
        static::assertEquals($type, $item->getType());
        static::assertEquals($id, $item->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_a_morph_to_relation()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $hydrator = new Hydrator($typeMapper, new LinksParser());

        $childJsonApiItem = $this->getJsonApiChildItemMock(3);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToRelation::class, $masterItem->getRelation('morph'));

        static::assertSame($childItem, $masterItem->getRelation('morph')->getIncluded());
        static::assertEquals('child', $masterItem->getRelation('morph')->getIncluded()->getType());
        static::assertEquals(3, $masterItem->getRelation('morph')->getIncluded()->getId());

        static::assertEquals(3, $masterItem->morph->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_a_morph_to_many_relation()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildItem::class);
        $typeMapper->setMapping('master', MasterItem::class);
        $hydrator = new Hydrator($typeMapper, new LinksParser());

        $childJsonApiItem = $this->getJsonApiChildItemMock(4);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToManyRelation::class, $masterItem->getRelation('morphmany'));

        static::assertSame($childItem, $masterItem->getRelation('morphmany')->getIncluded()[0]);
        static::assertEquals('child', $masterItem->getRelation('morphmany')->getIncluded()[0]->getType());
        static::assertEquals(4, $masterItem->getRelation('morphmany')->getIncluded()[0]->getId());

        static::assertEquals(4, $masterItem->morphmany[0]->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_an_unknown_relation_as_morph_to()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item-without-relationships', WithoutRelationshipsItem::class);
        $hydrator = new Hydrator($typeMapper, new LinksParser());

        $childJsonApiItem = $this->getJsonApiChildItemMock(3);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToRelation::class, $masterItem->getRelation('morph'));

        static::assertSame($childItem, $masterItem->getRelation('morph')->getIncluded());
        static::assertEquals('child', $masterItem->getRelation('morph')->getIncluded()->getType());
        static::assertEquals(3, $masterItem->getRelation('morph')->getIncluded()->getId());

        static::assertEquals(3, $masterItem->morph->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_an_unknown_relation_as_morph_to_many()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Client\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item-without-relationships', WithoutRelationshipsItem::class);
        $hydrator = new Hydrator($typeMapper, new LinksParser());

        $childJsonApiItem = $this->getJsonApiChildItemMock(4);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToManyRelation::class, $masterItem->getRelation('morphmany'));

        static::assertSame($childItem, $masterItem->getRelation('morphmany')->getIncluded()[0]);
        static::assertEquals('child', $masterItem->getRelation('morphmany')->getIncluded()[0]->getType());
        static::assertEquals(4, $masterItem->getRelation('morphmany')->getIncluded()[0]->getId());

        static::assertEquals(4, $masterItem->morphmany[0]->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_links()
    {
        $hydrator = $this->getHydrator();

        $jsonApiItem = $this->getJsonApiItemMock('master', 1);
        $item = $hydrator->hydrateItem($jsonApiItem);

        static::assertInstanceOf(Links::class, $item->getLinks());

        static::assertEquals(new Links(['self' => new Link('http://example.com/master/1')]), $item->getLinks());
    }

    /**
     * @test
     */
    public function it_hydrates_meta()
    {
        $hydrator = $this->getHydrator();

        $jsonApiItem = $this->getJsonApiItemMock('master', 1);
        $item = $hydrator->hydrateItem($jsonApiItem);

        static::assertInstanceOf(Meta::class, $item->getMeta());

        static::assertEquals(new Meta(['foo' => 'bar']), $item->getMeta());
    }
}
