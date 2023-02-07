<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Exceptions\MassAssignmentException;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\ParentItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\RelatedItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithHiddenItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithRelationshipItem;
use Swis\JsonApi\Client\Tests\Mocks\ItemStub;

class ItemTest extends TestCase
{
    /**
     * @test
     */
    public function itCanInstantiateAnItem()
    {
        $item = new Item(['name' => 'john']);
        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals('john', $item->name);
    }

    /**
     * @test
     */
    public function itCanCreateANewInstanceWithAttributes()
    {
        $item = new ItemStub();
        $item->setType('foo-bar');
        $instance = $item->newInstance(['name' => 'john']);

        $this->assertInstanceOf(ItemStub::class, $instance);
        $this->assertEquals($item->getType(), $instance->getType());
        $this->assertEquals('john', $instance->name);
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
        $parentItem = new ParentItem();
        $childItem = new ChildItem();
        $parentItem->child()->associate($childItem);

        $this->assertSame($childItem, $parentItem->getAttribute('child'));
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
        $parentItem = new ParentItem();
        $childItem = new ChildItem();
        $childItem->setId('1');
        $childItem->setMeta(new Meta(['foo' => 'bar']));
        $parentItem->child()->associate($childItem);
        $parentItem->children()->associate(new Collection([$childItem]));

        $relations = $parentItem->getRelationships();

        $this->assertSame([
            'child' => [
                'data' => [
                    'type' => 'child',
                    'id' => '1',
                    'meta' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'children' => [
                'data' => [
                    [
                        'type' => 'child',
                        'id' => '1',
                        'meta' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ], $relations);
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasRelationships()
    {
        $parentItem = new ParentItem();
        $this->assertFalse($parentItem->hasRelationships());

        $childItem = (new ChildItem())->setId('1');
        $parentItem->child()->associate($childItem);

        $this->assertTrue($parentItem->hasRelationships());
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
    public function itReturnsNullWhenProvidedAnEmptyKey()
    {
        $item = new Item();

        $this->assertNull($item->getAttribute(''));
    }

    /**
     * @test
     */
    public function itReturnsNullWhenProvidedAKeyThatIsAMethodOnTheItem()
    {
        $item = new Item();

        $this->assertNull($item->getAttribute('getAttribute'));
    }

    /**
     * @test
     */
    public function itCanManipulateAttributes()
    {
        $item = new ItemStub();
        $item->name = 'foo';

        $this->assertEquals('foo', $item->name);
        $this->assertTrue(isset($item->name));
        unset($item->name);
        $this->assertEquals(null, $item->name);
        $this->assertFalse(isset($item->name));

        $item['name'] = 'foo';
        $this->assertTrue(isset($item['name']));
        unset($item['name']);
        $this->assertFalse(isset($item['name']));
    }

    /**
     * @test
     */
    public function itDoesNotShowHiddenAttributes()
    {
        $item = new ItemStub();
        $item->password = 'secret';

        $attributes = $item->attributesToArray();
        $this->assertFalse(isset($attributes['password']));
        $this->assertEquals(['password'], $item->getHidden());
    }

    /**
     * @test
     */
    public function itDoesShowVisibleAttributes()
    {
        $item = new ItemStub();
        $item->setVisible(['name']);
        $item->name = 'John Doe';
        $item->city = 'Paris';

        $attributes = $item->attributesToArray();
        $this->assertEquals(['name' => 'John Doe'], $attributes);
    }

    /**
     * @test
     */
    public function itCanReturnTheItemInArrayForm()
    {
        $item = new ItemStub();
        $item->name = 'foo';
        $item->bar = null;
        $item->password = 'password1';
        $item->setHidden(['password']);
        $item->setVisible(['name']);
        $array = $item->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('foo', $array['name']);
        $this->assertFalse(isset($array['password']));
        $this->assertEquals($array, $item->jsonSerialize());

        $item->makeHidden(['name']);
        $item->makeVisible('password');
        $array = $item->toArray();
        $this->assertIsArray($array);
        $this->assertFalse(isset($array['name']));
        $this->assertTrue(isset($array['password']));
    }

    /**
     * @test
     */
    public function itCanReturnASubsetOfTheItemInArrayForm()
    {
        $item = new ItemStub();
        $item->name = 'foo';
        $item->bar = null;
        $array = $item->only('name');

        $this->assertIsArray($array);
        $this->assertEquals(['name' => 'foo'], $array);
        $this->assertFalse(isset($array['bar']));
    }

    /**
     * @test
     */
    public function itCanReturnTheItemInJsonForm()
    {
        $item = new ItemStub();
        $item->name = 'john';
        $item->foo = 10;

        $object = new \stdClass();
        $object->name = 'john';
        $object->foo = 10;

        $this->assertEquals(json_encode($object), $item->toJson());
        $this->assertEquals(json_encode($object), (string) $item);
    }

    /**
     * @test
     */
    public function itCanMutateAttributes()
    {
        $item = new ItemStub();
        $item->list_items = ['name' => 'john'];
        $this->assertEquals(['name' => 'john'], $item->list_items);
        $attributes = $item->getAttributes();
        $this->assertEquals(json_encode(['name' => 'john']), $attributes['list_items']);

        $birthday = strtotime('245 months ago');

        $item = new ItemStub();
        $item->birthday = '245 months ago';

        $this->assertEquals(date('Y-m-d', $birthday), $item->birthday);
        $this->assertEquals(20, $item->age);
    }

    /**
     * @test
     */
    public function itUsesMutatorsInToArray()
    {
        $item = new ItemStub();
        $item->list_items = [1, 2, 3];
        $array = $item->toArray();

        $this->assertEquals([1, 2, 3], $array['list_items']);
    }

    /**
     * @test
     */
    public function itCanReplicate()
    {
        $item = new ItemStub();
        $item->name = 'John Doe';
        $item->city = 'Paris';

        $clone = $item->replicate();
        $this->assertEquals($item, $clone);
        $this->assertEquals($item->name, $clone->name);
    }

    /**
     * @test
     */
    public function itAppendsMutators()
    {
        $item = new ItemStub();
        $array = $item->toArray();
        $this->assertFalse(isset($array['test']));

        $item = new ItemStub();
        $item->setAppends(['test']);
        $array = $item->toArray();
        $this->assertTrue(isset($array['test']));
        $this->assertEquals('test', $array['test']);
    }

    /**
     * @test
     */
    public function itCanMergeAppends()
    {
        $item = new ItemStub();
        $item->mergeAppends([__FUNCTION__]);
        $this->assertTrue($item->hasAppended(__FUNCTION__));
    }

    /**
     * @test
     */
    public function itCanUseArrayAccess()
    {
        $item = new ItemStub();
        $item->name = 'John Doen';
        $item['city'] = 'Paris';

        $this->assertEquals($item->name, $item['name']);
        $this->assertEquals($item->city, $item['city']);
    }

    /**
     * @test
     */
    public function itCanBeSerializedAndUnserialized()
    {
        $item = new ItemStub();
        $item->name = 'john';
        $item->foo = 10;

        $serialized = serialize($item);
        $this->assertEquals($item, unserialize($serialized));
    }

    /**
     * @test
     */
    public function itCanCastAttributes()
    {
        $item = new ItemStub();
        $item->score = '0.34';
        $item->score_inf = 'Infinity';
        $item->score_inf_neg = '-Infinity';
        $item->score_nan = 'NaN';
        $item->data = ['foo' => 'bar'];
        $item->count = 1;
        $item->object_data = ['foo' => 'bar'];
        $item->active = 'true';
        $item->default = 'bar';
        $item->collection_data = [['foo' => 'bar', 'baz' => 'bat']];

        $this->assertIsFloat($item->score);
        $this->assertIsFloat($item->score_inf);
        $this->assertEquals(INF, $item->score_inf);
        $this->assertIsFloat($item->score_inf_neg);
        $this->assertEquals(-INF, $item->score_inf_neg);
        $this->assertIsFloat($item->score_nan);
        $this->assertNan($item->score_nan);
        $this->assertIsArray($item->data);
        $this->assertIsBool($item->active);
        $this->assertIsInt($item->count);
        $this->assertEquals('bar', $item->default);
        $this->assertInstanceOf(\stdClass::class, $item->object_data);
        $this->assertInstanceOf(Collection::class, $item->collection_data);

        $attributes = $item->getAttributes();
        $this->assertIsString($attributes['score']);
        $this->assertIsString($attributes['score_inf']);
        $this->assertIsString($attributes['score_inf_neg']);
        $this->assertIsString($attributes['score_nan']);
        $this->assertIsString($attributes['data']);
        $this->assertIsString($attributes['active']);
        $this->assertIsInt($attributes['count']);
        $this->assertIsString($attributes['default']);
        $this->assertIsString($attributes['object_data']);
        $this->assertIsString($attributes['collection_data']);

        $array = $item->toArray();
        $this->assertIsFloat($array['score']);
        $this->assertIsFloat($array['score_inf']);
        $this->assertIsFloat($array['score_inf_neg']);
        $this->assertIsFloat($array['score_nan']);
        $this->assertIsArray($array['data']);
        $this->assertIsBool($array['active']);
        $this->assertIsInt($array['count']);
        $this->assertEquals('bar', $array['default']);
        $this->assertInstanceOf(\stdClass::class, $array['object_data']);
        $this->assertIsArray($array['collection_data']);
    }

    /**
     * @test
     */
    public function itCanMergeCasts()
    {
        $item = new ItemStub();
        $item->mergeCasts([__FUNCTION__ => 'int']);
        $this->assertArrayHasKey(__FUNCTION__, $item->getCasts());
    }

    /**
     * @test
     */
    public function itCanGuardAttributes()
    {
        $item = new ItemStub(['secret' => 'foo']);
        $this->assertTrue($item->isGuarded('secret'));
        $this->assertNull($item->secret);
        $this->assertContains('secret', $item->getGuarded());

        $item->secret = 'bar';
        $this->assertEquals('bar', $item->secret);

        ItemStub::unguard();

        $this->assertTrue(ItemStub::isUnguarded());
        $item = new ItemStub(['secret' => 'foo']);
        $this->assertEquals('foo', $item->secret);

        ItemStub::reguard();
    }

    /**
     * @test
     */
    public function itCanMergeGuarded()
    {
        $item = new Item();
        $item->guard([]);
        $this->assertFalse($item->isGuarded('foo'));

        $item->guard(['id']);
        $item->mergeGuarded(['foo']);

        $this->assertEquals(['id', 'foo'], $item->getGuarded());
        $this->assertTrue($item->isGuarded('id'));
        $this->assertTrue($item->isGuarded('foo'));
    }

    /**
     * @test
     */
    public function itCanUseTheGuardedCallback()
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['callback'])
            ->getMock();

        $mock->expects($this->once())
            ->method('callback')
            ->willReturn('foo');

        ItemStub::unguard();
        $string = ItemStub::unguarded([$mock, 'callback']);
        $this->assertEquals('foo', $string);
        ItemStub::reguard();
    }

    /**
     * @test
     */
    public function itCanBeTotallyGuarded()
    {
        $this->expectException(MassAssignmentException::class);

        $item = new ItemStub();
        $item->guard(['*']);
        $item->fillable([]);
        $item->fill(['name' => 'John Doe']);
    }

    /**
     * @test
     */
    public function itCanBeFillable()
    {
        $item = new ItemStub(['foo' => 'bar']);
        $this->assertFalse($item->isFillable('foo'));
        $this->assertNull($item->foo);
        $this->assertNotContains('foo', $item->getFillable());

        $item->foo = 'bar';
        $this->assertEquals('bar', $item->foo);

        $item = new ItemStub();
        $item->forceFill(['foo' => 'bar']);
        $this->assertEquals('bar', $item->foo);
    }

    /**
     * @test
     */
    public function itCanMergeFillable()
    {
        $item = new ItemStub();
        $item->fillable(['foo']);
        $item->mergeFillable(['bar']);
        $this->assertEquals(['foo', 'bar'], $item->getFillable());
    }

    /**
     * @test
     */
    public function itCanHydrateAnArrayOfAttributes()
    {
        $items = ItemStub::hydrate([['name' => 'John Doe']]);
        $this->assertInstanceOf(ItemStub::class, $items[0]);
        $this->assertEquals('John Doe', $items[0]->name);
    }
}
