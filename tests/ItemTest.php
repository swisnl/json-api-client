<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\RelatedItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithGetMutatorItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithHiddenItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithRelationshipItem;

class ItemTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_can_instantiate_an_item()
    {
        $item = new Item();
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @test
     */
    public function is_shows_type_and_id_and_attributes_in_to_json_api_array()
    {
        $attributes = [
            'testKey' => 'testValue',
            'boolean' => true,
            'object'  => [
                'foo' => 'bar',
            ],
            'array'   => [1, 2, 3],
        ];
        $item = new Item($attributes);
        $item->setType('testType');
        $item->setId('1234');

        $this->assertSame(
            [
                'type'       => 'testType',
                'id'         => '1234',
                'attributes' => $attributes,
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_does_not_show_attributes_in_to_json_api_array_when_it_has_no_attributes()
    {
        $item = new WithHiddenItem(['testKey' => 'testValue']);
        $item->setType('testType');
        $item->setId('1234');

        $this->assertSame(
            [
                'type' => 'testType',
                'id'   => '1234',
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_hasone_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasoneRelation()->associate((new RelatedItem())->setId('5678'));

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'hasone_relation' => [
                        'data' => [
                            'type' => 'related-item',
                            'id'   => '5678',
                        ],
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_empty_hasone_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasoneRelation()->dissociate();

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'hasone_relation' => [
                        'data' => null,
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_hasmany_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasmanyRelation()->associate(new Collection([(new RelatedItem())->setId('5678')]));

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'hasmany_relation' => [
                        'data' => [
                            [
                                'type' => 'related-item',
                                'id'   => '5678',
                            ],
                        ],
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_empty_hasmany_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasmanyRelation()->dissociate();

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'hasmany_relation' => [
                        'data' => [],
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_morphto_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtoRelation()->associate((new RelatedItem())->setId('5678'));

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'morphto_relation' => [
                        'data' => [
                            'type' => 'related-item',
                            'id'   => '5678',
                        ],
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_empty_morphto_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtoRelation()->dissociate();

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'morphto_relation' => [
                        'data' => null,
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_morphtomany_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtomanyRelation()->associate(new Collection([(new RelatedItem())->setId('5678')]));

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'morphtomany_relation' => [
                        'data' => [
                            [
                                'type' => 'related-item',
                                'id'   => '5678',
                            ],
                        ],
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_empty_morphtomany_relation_in_to_json_api_array()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtomanyRelation()->dissociate();

        $this->assertSame(
            [
                'type'          => 'item-with-relationship',
                'id'            => '1234',
                'relationships' => [
                    'morphtomany_relation' => [
                        'data' => [],
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_links_in_to_json_api_array()
    {
        $item = new Item();
        $item->setType('testType');
        $item->setId('1');
        $item->setLinks(
            new Links(
                [
                    'self' => new Link(
                        'http://example.com/testType/1',
                        new Meta(['foo' => 'bar'])
                    ),
                    'other' => new Link('http://example.com/testType/1/other'),
                ]
            )
        );

        $this->assertSame(
            [
                'type'  => 'testType',
                'id'    => '1',
                'links' => [
                    'self' => [
                        'href' => 'http://example.com/testType/1',
                        'meta' => [
                            'foo' => 'bar',
                        ],
                    ],
                    'other' => [
                        'href' => 'http://example.com/testType/1/other',
                    ],
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function is_adds_meta_in_to_json_api_array()
    {
        $item = new Item();
        $item->setType('testType');
        $item->setId('1');
        $item->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'testType',
                'id'   => '1',
                'meta' => [
                    'foo' => 'bar',
                ],
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function it_is_new_when_no_id_isset()
    {
        $item = new Item();
        $item->setType('testType');

        $this->assertTrue($item->isNew());

        $item->setId('1');
        $this->assertFalse($item->isNew());
    }

    /**
     * @test
     */
    public function it_can_get_a_relation_value_using_get_attribute_method()
    {
        $masterItem = new MasterItem();
        $childItem = new ChildItem();
        $masterItem->child()->associate($childItem);

        $this->assertSame($childItem, $masterItem->getAttribute('child'));
    }

    /**
     * @test
     */
    public function it_returns_attributes()
    {
        $attributes = [
            'foo' => 'bar',
        ];
        $item = new Item($attributes);
        $this->assertEquals($attributes, $item->getAttributes());
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_indicating_if_it_has_attributes()
    {
        $item = new Item();
        $this->assertFalse($item->hasAttributes());

        $item->fill(['foo' => 'bar']);

        $this->assertTrue($item->hasAttributes());
    }

    /**
     * @test
     */
    public function it_can_get_all_relationships()
    {
        $masterItem = new MasterItem();
        $childItem = new ChildItem();
        $childItem->setId('1');
        $masterItem->child()->associate($childItem);

        $relations = $masterItem->getRelationships();

        $this->assertSame([
            'child' => [
                'data' => [
                    'type' => 'child',
                    'id'   => '1',
                ],
            ],
        ], $relations);
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_indicating_if_it_has_relationships()
    {
        $masterItem = new MasterItem();
        $this->assertFalse($masterItem->hasRelationships());

        $childItem = (new ChildItem())->setId('1');
        $masterItem->child()->associate($childItem);

        $this->assertTrue($masterItem->hasRelationships());
    }

    /**
     * @test
     */
    public function it_uses_initial_values()
    {
        $itemBuilder = new Item();
        $itemBuilder->fill(['testKey' => 1, 'anotherTestKey' => 'someValue']);
        $itemBuilder->setInitial(['testKey' => 9999]);
        $itemBuilder->useInitial();

        $this->assertEquals(['testKey' => 9999, 'anotherTestKey' => 'someValue'], $itemBuilder->getAttributes());
    }

    /**
     * @test
     */
    public function it_can_set_the_id_using_the_magic_method()
    {
        $item = new Item();

        $item->id = '1234';
        $this->assertEquals('1234', $item->getId());
    }

    /**
     * @test
     */
    public function it_can_get_the_id_using_the_magic_method()
    {
        $item = new Item();
        $item->setId('1234');

        $this->assertEquals('1234', $item->id);
    }

    /**
     * @test
     */
    public function it_can_check_if_the_id_is_set_using_the_magic_method()
    {
        $item = new Item();

        $this->assertFalse(isset($item->id));
        $item->setId('1234');
        $this->assertTrue(isset($item->id));
    }

    /**
     * @test
     */
    public function it_can_unset_the_id_using_the_magic_method()
    {
        $item = new Item();

        $item->id = '1234';
        unset($item->id);
        $this->assertNull($item->getId());
    }

    /**
     * @test
     */
    public function it_can_set_an_attribute_using_the_magic_method()
    {
        $item = new Item();

        $item->foo = 'bar';
        $this->assertEquals('bar', $item->getAttribute('foo'));
    }

    /**
     * @test
     */
    public function it_can_get_an_attribute_using_the_magic_method()
    {
        $item = new Item();
        $item->setAttribute('foo', 'bar');

        $this->assertEquals('bar', $item->foo);
    }

    /**
     * @test
     */
    public function it_can_check_if_an_attribute_is_set_using_the_magic_method()
    {
        $item = new Item();

        $this->assertFalse(isset($item->foo));
        $item->setAttribute('foo', 'bar');
        $this->assertTrue(isset($item->foo));
    }

    /**
     * @test
     */
    public function it_can_unset_an_attribute_using_the_magic_method()
    {
        $item = new Item();

        $item->setAttribute('foo', 'bar');
        $this->assertNotNull($item->getAttribute('foo'));

        unset($item->foo);
        $this->assertNull($item->getAttribute('foo'));
    }

    /**
     * @test
     */
    public function it_returns_attribute_from_get_mutator()
    {
        $item = new WithGetMutatorItem();

        $this->assertEquals('test', $item->getAttribute('test_attribute'));
    }
}
