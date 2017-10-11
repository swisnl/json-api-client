<?php

namespace Swis\JsonApi\Tests\JsonApi;

use Art4\JsonApiClient\Resource\CollectionInterface;
use Art4\JsonApiClient\Utils\Manager;
use Swis\JsonApi\Collection;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\JsonApi\Hydrator;
use Swis\JsonApi\Relations\HasOneRelation;
use Swis\JsonApi\Relations\MorphToManyRelation;
use Swis\JsonApi\Relations\MorphToRelation;
use Swis\JsonApi\Tests\AbstractTest;
use Swis\JsonApi\Tests\Mocks\Items\Jenssegers\ChildJenssegersItem;
use Swis\JsonApi\Tests\Mocks\Items\Jenssegers\MasterJenssegersItem;
use Swis\JsonApi\Tests\Mocks\Items\Jenssegers\WithoutRelationshipsJenssegersItem;
use Swis\JsonApi\TypeMapper;

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

            static::assertInstanceOf(JenssegersItem::class, $item);
            static::assertEquals($type, $item->getType());
            static::assertEquals($id, $item->getId());
        }
    }

    /**
     * @return \Swis\JsonApi\JsonApi\Hydrator
     */
    protected function getHydrator(): Hydrator
    {
        return new Hydrator($this->getTypeMapperMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Swis\JsonApi\Interfaces\TypeMapperInterface
     */
    protected function getTypeMapperMock()
    {
        $typeMapper = $this->createMock(TypeMapper::class);
        $typeMapper->method('hasMapping')->willReturn(true);
        $typeMapper->method('getMapping')->will(
            $this->returnCallback(
                function () {
                    return new JenssegersItem();
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
                    ],
                    'morph'     => [
                        'data' => [
                            'type' => 'child',
                            'id'   => '3',
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
                    ],
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
        /** @var \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildJenssegersItem::class);
        $typeMapper->setMapping('master', MasterJenssegersItem::class);
        $hydrator = new Hydrator($typeMapper);

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

        static::assertInstanceOf(MasterJenssegersItem::class, $masterItem);
        static::assertInstanceOf(HasOneRelation::class, $masterItem->getRelationship('child'));

        static::assertSame($childItem, $masterItem->getRelationship('child')->getIncluded());
        static::assertEquals('child', $masterItem->getRelationship('child')->getIncluded()->getType());
        static::assertEquals(2, $masterItem->getRelationship('child')->getIncluded()->getId());
        static::assertSame($masterItem, $masterItem->getRelationship('child')->getIncluded()->getRelationship('parent')->getIncluded());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Art4\JsonApiClient\Resource\CollectionInterface
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
            static::assertInstanceOf(JenssegersItem::class, $item);
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

        static::assertInstanceOf(JenssegersItem::class, $item);
        static::assertEquals($type, $item->getType());
        static::assertEquals($id, $item->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_a_morph_to_relation()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildJenssegersItem::class);
        $typeMapper->setMapping('master', MasterJenssegersItem::class);
        $hydrator = new Hydrator($typeMapper);

        $childJsonApiItem = $this->getJsonApiChildItemMock(3);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToRelation::class, $masterItem->getRelationship('morph'));

        static::assertSame($childItem, $masterItem->getRelationship('morph')->getIncluded());
        static::assertEquals('child', $masterItem->getRelationship('morph')->getIncluded()->getType());
        static::assertEquals(3, $masterItem->getRelationship('morph')->getIncluded()->getId());

        static::assertEquals(3, $masterItem->morph->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_a_morph_to_many_relation()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('child', ChildJenssegersItem::class);
        $typeMapper->setMapping('master', MasterJenssegersItem::class);
        $hydrator = new Hydrator($typeMapper);

        $childJsonApiItem = $this->getJsonApiChildItemMock(4);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToManyRelation::class, $masterItem->getRelationship('morphmany'));

        static::assertSame($childItem, $masterItem->getRelationship('morphmany')->getIncluded()[0]);
        static::assertEquals('child', $masterItem->getRelationship('morphmany')->getIncluded()[0]->getType());
        static::assertEquals(4, $masterItem->getRelationship('morphmany')->getIncluded()[0]->getId());

        static::assertEquals(4, $masterItem->morphmany[0]->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_an_unknown_relation_as_morph_to()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item-without-relationships', WithoutRelationshipsJenssegersItem::class);
        $hydrator = new Hydrator($typeMapper);

        $childJsonApiItem = $this->getJsonApiChildItemMock(3);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToRelation::class, $masterItem->getRelationship('morph'));

        static::assertSame($childItem, $masterItem->getRelationship('morph')->getIncluded());
        static::assertEquals('child', $masterItem->getRelationship('morph')->getIncluded()->getType());
        static::assertEquals(3, $masterItem->getRelationship('morph')->getIncluded()->getId());

        static::assertEquals(3, $masterItem->morph->getId());
    }

    /**
     * @test
     */
    public function it_hydrates_an_unknown_relation_as_morph_to_many()
    {
        // Register the mocked type
        /** @var \Swis\JsonApi\Interfaces\TypeMapperInterface $typeMapper */
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item-without-relationships', WithoutRelationshipsJenssegersItem::class);
        $hydrator = new Hydrator($typeMapper);

        $childJsonApiItem = $this->getJsonApiChildItemMock(4);
        $childItem = $hydrator->hydrateItem($childJsonApiItem);
        $masterJsonApiItem = $this->getJsonApiItemMock('master', 1);
        $masterItem = $hydrator->hydrateItem($masterJsonApiItem);

        $hydrator->hydrateRelationships(
            new Collection([$childJsonApiItem, $masterJsonApiItem]),
            new Collection([$childItem, $masterItem])
        );

        static::assertInstanceOf(MorphToManyRelation::class, $masterItem->getRelationship('morphmany'));

        static::assertSame($childItem, $masterItem->getRelationship('morphmany')->getIncluded()[0]);
        static::assertEquals('child', $masterItem->getRelationship('morphmany')->getIncluded()[0]->getType());
        static::assertEquals(4, $masterItem->getRelationship('morphmany')->getIncluded()[0]->getId());

        static::assertEquals(4, $masterItem->morphmany[0]->getId());
    }
}
