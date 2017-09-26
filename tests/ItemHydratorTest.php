<?php

namespace Swis\JsonApi\Tests;

use InvalidArgumentException;
use Swis\JsonApi\Collection;
use Swis\JsonApi\ItemHydrator;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\Relations\HasManyRelation;
use Swis\JsonApi\Relations\HasOneRelation;
use Swis\JsonApi\Relations\MorphToManyRelation;
use Swis\JsonApi\Relations\MorphToRelation;
use Swis\JsonApi\Tests\Mocks\Items\Jenssegers\AnotherRelatedJenssegersItem;
use Swis\JsonApi\Tests\Mocks\Items\Jenssegers\RelatedJenssegersItem;
use Swis\JsonApi\Tests\Mocks\Items\Jenssegers\WithRelationshipJenssegersItem;
use Swis\JsonApi\TypeMapper;

class ItemHydratorTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_hydrates_items_without_relationships()
    {
        $data = [
            'testattribute1' => 'test',
            'testattribute2' => 'test2',
        ];

        $item = new JenssegersItem();

        $item = $this->getItemHydrator()->hydrate($item, $data);

        $this->assertEquals($data, $item->getAttributes());
    }

    /**
     * @return \Swis\JsonApi\ItemHydrator
     */
    private function getItemHydrator()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('hydratedItem', JenssegersItem::class);

        $typeMapper->setMapping('related-item', RelatedJenssegersItem::class);
        $typeMapper->setMapping('another-related-item', AnotherRelatedJenssegersItem::class);

        return new ItemHydrator($typeMapper);
    }

    /**
     * @test
     */
    public function it_hydrates_items_with_hasone_relationships()
    {
        $data = [
            'testattribute1'  => 'test',
            'testattribute2'  => 'test2',
            'hasone_relation' => 1,
        ];

        $item = new WithRelationshipJenssegersItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Relations\HasOneRelation $hasOne */
        $hasOne = $item->getRelationship('hasone_relation');

        $this->assertInstanceOf(
            HasOneRelation::class,
            $hasOne
        );

        $this->assertEquals($data['testattribute1'], $item->getAttribute('testattribute1'));
        $this->assertEquals($data['testattribute2'], $item->getAttribute('testattribute2'));

        $this->assertEquals($data['hasone_relation'], $hasOne->getId());
        $this->assertEquals('related-item', $hasOne->getType());
        $this->assertArrayHasKey('hasone_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_hydrates_items_with_hasmany_relationships()
    {
        $data = [
            'testattribute1'   => 'test',
            'testattribute2'   => 'test2',
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

        $item = new WithRelationshipJenssegersItem();

        $item = $this->getItemHydrator()->hydrate($item, $data);
        /** @var \Swis\JsonApi\Relations\HasManyRelation $hasMany */
        $hasMany = $item->getRelationship('hasmany_relation');

        $this->assertInstanceOf(
            HasManyRelation::class,
            $hasMany
        );

        $this->assertInstanceOf(Collection::class, $hasMany->getIncluded());
        $this->assertCount(2, $hasMany->getIncluded());

        $expected = [
            [
                'id'         => 1,
                'type'       => 'related-item',
                'attributes' => [
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
            ],
            [
                'id'         => 2,
                'type'       => 'related-item',
                'attributes' => [
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
            ],
        ];

        $this->assertEquals($expected, $hasMany->getIncluded()->toJsonApiArray());
        $this->assertArrayHasKey('hasmany_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_morphto_relationship_without_type_attribute()
    {
        $data = [
            'testattribute1'   => 'test',
            'testattribute2'   => 'test2',
            'morphto_relation' => [
                'id'                      => 1,
                'test_related_attribute1' => 'test',
            ],
        ];

        $item = new WithRelationshipJenssegersItem();

        $this->expectException(InvalidArgumentException::class);
        $this->getItemHydrator()->hydrate($item, $data);
    }

    /**
     * @test
     */
    public function it_hydrates_items_with_morphto_relationship()
    {
        $data = [
            'testattribute1'   => 'test',
            'testattribute2'   => 'test2',
            'morphto_relation' => [
                'id'                      => 1,
                'type'                    => 'related-item',
                'test_related_attribute1' => 'test',
            ],
        ];

        $item = new WithRelationshipJenssegersItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Relations\MorphToRelation $morphTo */
        $morphTo = $item->getRelationship('morphto_relation');

        $this->assertInstanceOf(
            MorphToRelation::class,
            $morphTo
        );
        $this->assertEquals($data['testattribute1'], $item->getAttribute('testattribute1'));
        $this->assertEquals($data['testattribute2'], $item->getAttribute('testattribute2'));
        $this->assertEquals('related-item', $morphTo->getType());
        $this->assertEquals(
            $data['morphto_relation']['test_related_attribute1'],
            $morphTo->getIncluded()->getAttribute('test_related_attribute1')
        );
        $this->assertArrayHasKey('morphto_relation', $item->toJsonApiArray()['relationships']);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_morphtomany_relationship_without_type_attribute()
    {
        $data = [
            'testattribute1'       => 'test',
            'testattribute2'       => 'test2',
            'morphtomany_relation' => [
                [
                    'id'                      => 1,
                    'test_related_attribute1' => 'test',
                ],
            ],
        ];

        $item = new WithRelationshipJenssegersItem();

        $this->expectException(InvalidArgumentException::class);
        $this->getItemHydrator()->hydrate($item, $data);
    }

    /**
     * @test
     */
    public function it_hydrates_items_with_morphtomany_relationship()
    {
        $data = [
            'testattribute1'       => 'test',
            'testattribute2'       => 'test2',
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

        $item = new WithRelationshipJenssegersItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        /** @var \Swis\JsonApi\Relations\MorphToManyRelation $morphToMany */
        $morphToMany = $item->getRelationship('morphtomany_relation');

        $this->assertInstanceOf(
            MorphToManyRelation::class,
            $morphToMany
        );
        $this->assertInstanceOf(Collection::class, $morphToMany->getIncluded());
        $this->assertCount(2, $morphToMany->getIncluded());

        $this->assertEquals($data['testattribute1'], $item->getAttribute('testattribute1'));
        $this->assertEquals($data['testattribute2'], $item->getAttribute('testattribute2'));

        $this->assertEquals('related-item', $morphToMany->getIncluded()[0]->getType());
        $this->assertEquals('another-related-item', $morphToMany->getIncluded()[1]->getType());
        $this->assertEquals(
            $data['morphtomany_relation'][0]['test_related_attribute1'],
            $morphToMany->getIncluded()[0]->getAttribute('test_related_attribute1')
        );
        $this->assertEquals(
            $data['morphtomany_relation'][1]['test_related_attribute1'],
            $morphToMany->getIncluded()[1]->getAttribute('test_related_attribute1')
        );
        $this->assertArrayHasKey('morphtomany_relation', $item->toJsonApiArray()['relationships']);
    }
}
