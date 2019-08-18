<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Exceptions\HydrationException;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\ItemHydrator;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Tests\Mocks\Items\AnotherRelatedItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\RelatedItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithRelationshipItem;
use Swis\JsonApi\Client\TypeMapper;

class ItemHydratorTest extends AbstractTest
{
    /**
     * @return \Swis\JsonApi\Client\ItemHydrator
     */
    private function getItemHydrator()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('hydratedItem', Item::class);

        $typeMapper->setMapping('related-item', RelatedItem::class);
        $typeMapper->setMapping('another-related-item', AnotherRelatedItem::class);

        return new ItemHydrator($typeMapper);
    }

    /**
     * @test
     */
    public function it_hydrates_attributes()
    {
        $data = [
            'testattribute1' => 'test',
            'testattribute2' => 'test2',
            'testobject'     => [
                'foo' => 'bar',
            ],
            'testarray'      => [1, 2, 3],
        ];

        $item = new Item();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        $this->assertEquals($data, $item->getAttributes());
    }

    /**
     * @test
     */
    public function it_hydrates_hasone_relationships_by_id()
    {
        $data = [
            'hasone_relation' => 1,
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\HasOneRelation $hasOne */
        $hasOne = $item->getRelation('hasone_relation');
        $this->assertInstanceOf(HasOneRelation::class, $hasOne);

        $this->assertEquals($data['hasone_relation'], $hasOne->getIncluded()->getId());
        $this->assertEquals('related-item', $hasOne->getIncluded()->getType());

        $this->assertArrayHasKey('hasone_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_hasone_relationships_with_attributes()
    {
        $data = [
            'hasone_relation' => [
                'id'                      => 1,
                'test_related_attribute1' => 'test',
                'test_related_attribute2' => 'test2',
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\HasOneRelation $hasOne */
        $hasOne = $item->getRelation('hasone_relation');
        $this->assertInstanceOf(HasOneRelation::class, $hasOne);

        $this->assertEquals($data['hasone_relation']['id'], $hasOne->getIncluded()->getId());
        $this->assertEquals('related-item', $hasOne->getIncluded()->getType());
        $this->assertEquals($data['hasone_relation']['test_related_attribute1'], $hasOne->getIncluded()->getAttribute('test_related_attribute1'));
        $this->assertEquals($data['hasone_relation']['test_related_attribute2'], $hasOne->getIncluded()->getAttribute('test_related_attribute2'));

        $this->assertArrayHasKey('hasone_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_hasmany_relationships_by_id()
    {
        $data = [
            'hasmany_relation' => [
                1,
                2,
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\HasManyRelation $hasMany */
        $hasMany = $item->getRelation('hasmany_relation');
        $this->assertInstanceOf(HasManyRelation::class, $hasMany);

        $this->assertInstanceOf(Collection::class, $hasMany->getIncluded());
        $this->assertCount(2, $hasMany->getIncluded());

        $this->assertEquals($data['hasmany_relation'][0], $hasMany->getIncluded()->get(0)->getId());
        $this->assertEquals('related-item', $hasMany->getIncluded()->get(0)->getType());
        $this->assertEquals($data['hasmany_relation'][1], $hasMany->getIncluded()->get(1)->getId());
        $this->assertEquals('related-item', $hasMany->getIncluded()->get(1)->getType());

        $this->assertArrayHasKey('hasmany_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_hasmany_relationships_with_attributes()
    {
        $data = [
            'hasmany_relation' => [
                [
                    'id'                      => 1,
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
                [
                    'id'                      => 2,
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\HasManyRelation $hasMany */
        $hasMany = $item->getRelation('hasmany_relation');
        $this->assertInstanceOf(HasManyRelation::class, $hasMany);

        $this->assertInstanceOf(Collection::class, $hasMany->getIncluded());
        $this->assertCount(2, $hasMany->getIncluded());

        $this->assertEquals($data['hasmany_relation'][0]['id'], $hasMany->getIncluded()->get(0)->getId());
        $this->assertEquals('related-item', $hasMany->getIncluded()->get(0)->getType());
        $this->assertEquals($data['hasmany_relation'][0]['test_related_attribute1'], $hasMany->getIncluded()->get(0)->getAttribute('test_related_attribute1'));
        $this->assertEquals($data['hasmany_relation'][0]['test_related_attribute2'], $hasMany->getIncluded()->get(0)->getAttribute('test_related_attribute2'));
        $this->assertEquals($data['hasmany_relation'][1]['id'], $hasMany->getIncluded()->get(1)->getId());
        $this->assertEquals('related-item', $hasMany->getIncluded()->get(1)->getType());
        $this->assertEquals($data['hasmany_relation'][1]['test_related_attribute1'], $hasMany->getIncluded()->get(1)->getAttribute('test_related_attribute1'));
        $this->assertEquals($data['hasmany_relation'][1]['test_related_attribute2'], $hasMany->getIncluded()->get(1)->getAttribute('test_related_attribute2'));

        $this->assertArrayHasKey('hasmany_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_morphto_relationships_by_id()
    {
        $data = [
            'morphto_relation' => [
                'id'   => 1,
                'type' => 'related-item',
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\MorphToRelation $morphTo */
        $morphTo = $item->getRelation('morphto_relation');
        $this->assertInstanceOf(MorphToRelation::class, $morphTo);

        $this->assertEquals($data['morphto_relation']['id'], $morphTo->getIncluded()->getId());
        $this->assertEquals($data['morphto_relation']['type'], $morphTo->getIncluded()->getType());
        $this->assertArrayNotHasKey('type', $morphTo->getIncluded()->getAttributes());

        $this->assertArrayHasKey('morphto_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_morphto_relationships_with_attributes()
    {
        $data = [
            'morphto_relation' => [
                'id'                      => 1,
                'type'                    => 'related-item',
                'test_related_attribute1' => 'test',
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\MorphToRelation $morphTo */
        $morphTo = $item->getRelation('morphto_relation');
        $this->assertInstanceOf(MorphToRelation::class, $morphTo);

        $this->assertEquals($data['morphto_relation']['id'], $morphTo->getIncluded()->getId());
        $this->assertEquals($data['morphto_relation']['type'], $morphTo->getIncluded()->getType());
        $this->assertArrayNotHasKey('type', $morphTo->getIncluded()->getAttributes());
        $this->assertEquals($data['morphto_relation']['test_related_attribute1'], $morphTo->getIncluded()->getAttribute('test_related_attribute1'));

        $this->assertArrayHasKey('morphto_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_morphto_relationships_with_unmapped_items()
    {
        $data = [
            'morphto_relation' => [
                'id'   => 1,
                'type' => 'unmapped-item',
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\MorphToRelation $morphTo */
        $morphTo = $item->getRelation('morphto_relation');
        $this->assertInstanceOf(MorphToRelation::class, $morphTo);

        $this->assertEquals($data['morphto_relation']['id'], $morphTo->getIncluded()->getId());
        $this->assertEquals($data['morphto_relation']['type'], $morphTo->getIncluded()->getType());
        $this->assertArrayNotHasKey('type', $morphTo->getIncluded()->getAttributes());
    }

    /**
     * @test
     */
    public function it_throws_for_morphto_relationships_without_type_attribute()
    {
        $data = [
            'morphto_relation' => [
                'id'                      => 1,
                'test_related_attribute1' => 'test',
            ],
        ];

        $item = new WithRelationshipItem();

        $this->expectException(HydrationException::class);
        $this->getItemHydrator()->hydrate($item, $data);
    }

    /**
     * @test
     */
    public function it_hydrates_morphtomany_relationships_by_id()
    {
        $data = [
            'morphtomany_relation' => [
                [
                    'id'   => 1,
                    'type' => 'related-item',
                ],
                [
                    'id'   => 2,
                    'type' => 'another-related-item',
                ],
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\MorphToManyRelation $morphToMany */
        $morphToMany = $item->getRelation('morphtomany_relation');
        $this->assertInstanceOf(MorphToManyRelation::class, $morphToMany);

        $this->assertInstanceOf(Collection::class, $morphToMany->getIncluded());
        $this->assertCount(2, $morphToMany->getIncluded());

        $this->assertEquals($data['morphtomany_relation'][0]['id'], $morphToMany->getIncluded()->get(0)->getId());
        $this->assertEquals($data['morphtomany_relation'][0]['type'], $morphToMany->getIncluded()->get(0)->getType());
        $this->assertArrayNotHasKey('type', $morphToMany->getIncluded()->get(0)->getAttributes());
        $this->assertEquals($data['morphtomany_relation'][1]['id'], $morphToMany->getIncluded()->get(1)->getId());
        $this->assertEquals($data['morphtomany_relation'][1]['type'], $morphToMany->getIncluded()->get(1)->getType());
        $this->assertArrayNotHasKey('type', $morphToMany->getIncluded()->get(1)->getAttributes());

        $this->assertArrayHasKey('morphtomany_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_morphtomany_relationships_with_attributes()
    {
        $data = [
            'morphtomany_relation' => [
                [
                    'id'                      => 1,
                    'type'                    => 'related-item',
                    'test_related_attribute1' => 'test1',
                ],
                [
                    'id'                      => 2,
                    'type'                    => 'another-related-item',
                    'test_related_attribute1' => 'test2',
                ],
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\MorphToManyRelation $morphToMany */
        $morphToMany = $item->getRelation('morphtomany_relation');
        $this->assertInstanceOf(MorphToManyRelation::class, $morphToMany);

        $this->assertInstanceOf(Collection::class, $morphToMany->getIncluded());
        $this->assertCount(2, $morphToMany->getIncluded());

        $this->assertEquals($data['morphtomany_relation'][0]['id'], $morphToMany->getIncluded()->get(0)->getId());
        $this->assertEquals($data['morphtomany_relation'][0]['type'], $morphToMany->getIncluded()->get(0)->getType());
        $this->assertArrayNotHasKey('type', $morphToMany->getIncluded()->get(0)->getAttributes());
        $this->assertEquals($data['morphtomany_relation'][0]['test_related_attribute1'], $morphToMany->getIncluded()[0]->getAttribute('test_related_attribute1'));
        $this->assertEquals($data['morphtomany_relation'][1]['id'], $morphToMany->getIncluded()->get(1)->getId());
        $this->assertEquals($data['morphtomany_relation'][1]['type'], $morphToMany->getIncluded()->get(1)->getType());
        $this->assertArrayNotHasKey('type', $morphToMany->getIncluded()->get(1)->getAttributes());
        $this->assertEquals($data['morphtomany_relation'][1]['test_related_attribute1'], $morphToMany->getIncluded()[1]->getAttribute('test_related_attribute1'));

        $this->assertArrayHasKey('morphtomany_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_morphtomany_relationships_with_unmapped_items()
    {
        $data = [
            'morphtomany_relation' => [
                [
                    'id'   => 1,
                    'type' => 'unmapped-item',
                ],
                [
                    'id'   => 2,
                    'type' => 'unmapped-item',
                ],
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\MorphToManyRelation $morphToMany */
        $morphToMany = $item->getRelation('morphtomany_relation');

        $this->assertInstanceOf(Collection::class, $morphToMany->getIncluded());
        $this->assertCount(2, $morphToMany->getIncluded());

        $this->assertEquals($data['morphtomany_relation'][0]['id'], $morphToMany->getIncluded()->get(0)->getId());
        $this->assertEquals($data['morphtomany_relation'][0]['type'], $morphToMany->getIncluded()->get(0)->getType());
        $this->assertArrayNotHasKey('type', $morphToMany->getIncluded()->get(0)->getAttributes());
        $this->assertEquals($data['morphtomany_relation'][1]['id'], $morphToMany->getIncluded()->get(1)->getId());
        $this->assertEquals($data['morphtomany_relation'][1]['type'], $morphToMany->getIncluded()->get(1)->getType());
        $this->assertArrayNotHasKey('type', $morphToMany->getIncluded()->get(1)->getAttributes());
    }

    /**
     * @test
     */
    public function it_throws_for_morphtomany_relationships_without_type_attribute()
    {
        $data = [
            'morphtomany_relation' => [
                [
                    'id'                      => 1,
                    'test_related_attribute1' => 'test',
                ],
            ],
        ];

        $item = new WithRelationshipItem();

        $this->expectException(HydrationException::class);
        $this->getItemHydrator()->hydrate($item, $data);
    }

    /**
     * @test
     */
    public function it_hydrates_nested_relationship_items()
    {
        $data = [
            'hasone_relation' => [
                'id'              => 1,
                'parent_relation' => 5,
            ],
        ];

        $item = new WithRelationshipItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Client\Relations\HasOneRelation $hasOne */
        $hasOne = $item->getRelation('hasone_relation');
        $this->assertInstanceOf(HasOneRelation::class, $hasOne);

        $this->assertEquals($data['hasone_relation']['id'], $hasOne->getIncluded()->getId());
        $this->assertEquals('related-item', $hasOne->getIncluded()->getType());

        /** @var \Swis\JsonApi\Client\Relations\HasOneRelation $hasOneParent */
        $hasOneParent = $hasOne->getIncluded()->getRelation('parent_relation');
        $this->assertInstanceOf(HasOneRelation::class, $hasOneParent);

        $this->assertEquals($data['hasone_relation']['parent_relation'], $hasOneParent->getIncluded()->getId());
        $this->assertEquals('item-with-relationship', $hasOneParent->getIncluded()->getType());
    }

    /**
     * @test
     * @dataProvider provideIdArguments
     *
     * @param $givenId
     * @param $expectedId
     */
    public function it_hydrates_the_id_when_not_null_or_empty_string($givenId, $expectedId)
    {
        $item = new Item();
        $item = $this->getItemHydrator()->hydrate($item, [], $givenId);

        static::assertSame($expectedId, $item->getId());
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

    /**
     * @test
     */
    public function it_throws_when_relationship_is_present_in_available_relationships_but_the_method_does_not_exist()
    {
        $data = [
            'does_not_exist' => 1,
        ];

        $item = new MasterItem();

        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf('Method doesNotExist not found on %s', MasterItem::class));

        $this->getItemHydrator()->hydrate($item, $data);
    }
}
