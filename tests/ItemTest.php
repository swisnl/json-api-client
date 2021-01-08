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
    public function itCanInstantiateAnItem()
    {
        $item = new Item();
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @test
     */
    public function isShowsTypeAndIdAndAttributesInToJsonApiArray()
    {
        $attributes = [
            'testKey' => 'testValue',
            'boolean' => true,
            'object' => [
                'foo' => 'bar',
            ],
            'array' => [1, 2, 3],
        ];
        $item = new Item($attributes);
        $item->setType('testType');
        $item->setId('1234');

        $this->assertSame(
            [
                'type' => 'testType',
                'id' => '1234',
                'attributes' => $attributes,
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function isDoesNotShowAttributesInToJsonApiArrayWhenItHasNoAttributes()
    {
        $item = new WithHiddenItem(['testKey' => 'testValue']);
        $item->setType('testType');
        $item->setId('1234');

        $this->assertSame(
            [
                'type' => 'testType',
                'id' => '1234',
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function isAddsHasoneRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasoneRelation()->associate((new RelatedItem())->setId('5678'));
        $item->hasoneRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->hasoneRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
                'relationships' => [
                    'hasone_relation' => [
                        'data' => [
                            'type' => 'related-item',
                            'id' => '5678',
                        ],
                        'links' => [
                            'self' => [
                                'href' => 'http://example.com/articles',
                            ],
                        ],
                        'meta' => [
                            'foo' => 'bar',
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
    public function isAddsEmptyHasoneRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasoneRelation()->dissociate();

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
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
    public function isDoesNotAddHasoneRelationWithoutDataInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasoneRelation();
        $item->hasoneRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->hasoneRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function isAddsHasmanyRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasmanyRelation()->associate(new Collection([(new RelatedItem())->setId('5678')]));
        $item->hasmanyRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->hasmanyRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
                'relationships' => [
                    'hasmany_relation' => [
                        'data' => [
                            [
                                'type' => 'related-item',
                                'id' => '5678',
                            ],
                        ],
                        'links' => [
                            'self' => [
                                'href' => 'http://example.com/articles',
                            ],
                        ],
                        'meta' => [
                            'foo' => 'bar',
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
    public function isAddsEmptyHasmanyRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasmanyRelation()->dissociate();

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
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
    public function isDoesNotAddHasmanyRelationWithoutDataInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->hasmanyRelation();
        $item->hasmanyRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->hasmanyRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function isAddsMorphtoRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtoRelation()->associate((new RelatedItem())->setId('5678'));
        $item->morphtoRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->morphtoRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
                'relationships' => [
                    'morphto_relation' => [
                        'data' => [
                            'type' => 'related-item',
                            'id' => '5678',
                        ],
                        'links' => [
                            'self' => [
                                'href' => 'http://example.com/articles',
                            ],
                        ],
                        'meta' => [
                            'foo' => 'bar',
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
    public function isAddsEmptyMorphtoRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtoRelation()->dissociate();

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
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
    public function isDoesNotAddMorphtoRelationWithoutDataInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtoRelation();
        $item->morphtoRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->morphtoRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function isAddsMorphtomanyRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtomanyRelation()->associate(new Collection([(new RelatedItem())->setId('5678')]));
        $item->morphtomanyRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->morphtomanyRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
                'relationships' => [
                    'morphtomany_relation' => [
                        'data' => [
                            [
                                'type' => 'related-item',
                                'id' => '5678',
                            ],
                        ],
                        'links' => [
                            'self' => [
                                'href' => 'http://example.com/articles',
                            ],
                        ],
                        'meta' => [
                            'foo' => 'bar',
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
    public function isAddsEmptyMorphtomanyRelationInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtomanyRelation()->dissociate();

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
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
    public function isDoesNotAddMorphtomanyRelationWithoutDataInToJsonApiArray()
    {
        $item = new WithRelationshipItem();
        $item->setId('1234');
        $item->morphtomanyRelation();
        $item->morphtomanyRelation()->setLinks(new Links(['self' => new Link('http://example.com/articles')]));
        $item->morphtomanyRelation()->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'item-with-relationship',
                'id' => '1234',
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function isAddsLinksInToJsonApiArray()
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
                'type' => 'testType',
                'id' => '1',
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
    public function isAddsMetaInToJsonApiArray()
    {
        $item = new Item();
        $item->setType('testType');
        $item->setId('1');
        $item->setMeta(new Meta(['foo' => 'bar']));

        $this->assertSame(
            [
                'type' => 'testType',
                'id' => '1',
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
    public function itIsNewWhenNoIdIsset()
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
    public function itCanGetARelationValueUsingGetAttributeMethod()
    {
        $masterItem = new MasterItem();
        $childItem = new ChildItem();
        $masterItem->child()->associate($childItem);

        $this->assertSame($childItem, $masterItem->getAttribute('child'));
    }

    /**
     * @test
     */
    public function itReturnsAttributes()
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
    public function itReturnsABooleanIndicatingIfItHasAttributes()
    {
        $item = new Item();
        $this->assertFalse($item->hasAttributes());

        $item->fill(['foo' => 'bar']);

        $this->assertTrue($item->hasAttributes());
    }

    /**
     * @test
     */
    public function itCanGetAllRelationships()
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
                    'id' => '1',
                ],
            ],
        ], $relations);
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasRelationships()
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
    public function itUsesInitialValues()
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
    public function itCanSetTheIdUsingTheMagicMethod()
    {
        $item = new Item();

        $item->id = '1234';
        $this->assertEquals('1234', $item->getId());
    }

    /**
     * @test
     */
    public function itCanGetTheIdUsingTheMagicMethod()
    {
        $item = new Item();
        $item->setId('1234');

        $this->assertEquals('1234', $item->id);
    }

    /**
     * @test
     */
    public function itCanCheckIfTheIdIsSetUsingTheMagicMethod()
    {
        $item = new Item();

        $this->assertFalse(isset($item->id));
        $item->setId('1234');
        $this->assertTrue(isset($item->id));
    }

    /**
     * @test
     */
    public function itCanUnsetTheIdUsingTheMagicMethod()
    {
        $item = new Item();

        $item->id = '1234';
        unset($item->id);
        $this->assertNull($item->getId());
    }

    /**
     * @test
     */
    public function itCanSetAnAttributeUsingTheMagicMethod()
    {
        $item = new Item();

        $item->foo = 'bar';
        $this->assertEquals('bar', $item->getAttribute('foo'));
    }

    /**
     * @test
     */
    public function itCanGetAnAttributeUsingTheMagicMethod()
    {
        $item = new Item();
        $item->setAttribute('foo', 'bar');

        $this->assertEquals('bar', $item->foo);
    }

    /**
     * @test
     */
    public function itCanCheckIfAnAttributeIsSetUsingTheMagicMethod()
    {
        $item = new Item();

        $this->assertFalse(isset($item->foo));
        $item->setAttribute('foo', 'bar');
        $this->assertTrue(isset($item->foo));
    }

    /**
     * @test
     */
    public function itCanUnsetAnAttributeUsingTheMagicMethod()
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
    public function itReturnsAttributeFromGetMutator()
    {
        $item = new WithGetMutatorItem();

        $this->assertEquals('test', $item->getAttribute('test_attribute'));
    }
}
