<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Concerns\HasRelations;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;
use Swis\JsonApi\Client\Relations\HasManyRelation;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Relations\MorphToManyRelation;
use Swis\JsonApi\Client\Relations\MorphToRelation;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;

class HasRelationsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_and_set_an_item_as_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();

        $mock->setRelation('foo', $data);

        $relation = $mock->getRelation('foo');
        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertSame($data, $relation->getIncluded());
    }

    /**
     * @test
     */
    public function it_can_get_and_set_a_collection_as_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Collection();

        $mock->setRelation('foo', $data);

        $relation = $mock->getRelation('foo');
        $this->assertInstanceOf(MorphToManyRelation::class, $relation);
        $this->assertSame($data, $relation->getIncluded());
    }

    /**
     * @test
     */
    public function it_sets_the_links_on_the_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();
        $links = new Links([]);

        $mock->setRelation('foo', $data, $links);

        $relation = $mock->getRelation('foo');
        $this->assertSame($links, $relation->getLinks());
    }

    /**
     * @test
     */
    public function it_sets_the_meta_on_the_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();
        $meta = new Meta([]);

        $mock->setRelation('foo', $data, null, $meta);

        $relation = $mock->getRelation('foo');
        $this->assertSame($meta, $relation->getMeta());
    }

    /**
     * @test
     */
    public function it_can_get_all_relations()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();

        $mock->setRelation('foo', $data);
        $relation = $mock->getRelation('foo');

        $this->assertSame(['foo' => $relation], $mock->getRelations());
    }

    /**
     * @test
     */
    public function it_can_get_a_relation_value()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();

        $mock->setRelation('foo', $data);

        $this->assertSame($data, $mock->getRelationValue('foo'));
    }

    /**
     * @test
     */
    public function it_returns_null_when_getting_an_unexisting_relation_value()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $this->assertNull($mock->getRelationValue('foo'));
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_indicating_if_it_has_a_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();

        $this->assertFalse($mock->hasRelation('foo'));

        $mock->setRelation('foo', $data);

        $this->assertTrue($mock->hasRelation('foo'));
    }

    /**
     * @test
     */
    public function it_can_unset_a_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();

        $mock->setRelation('foo', $data);
        $this->assertNotNull($mock->getRelation('foo'));

        $mock->unsetRelation('foo');

        $this->assertNull($mock->getRelation('foo'));
    }

    /**
     * @test
     */
    public function it_can_define_a_has_one_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->hasOne(MasterItem::class, 'foo-bar');

        $this->assertInstanceOf(HasOneRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation('foo-bar'));
    }

    /**
     * @test
     */
    public function it_can_define_a_has_one_relation_with_the_calling_method_as_fallback_name()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->hasOne(MasterItem::class);

        $this->assertInstanceOf(HasOneRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(__FUNCTION__));
    }

    /**
     * @test
     */
    public function it_can_define_a_has_many_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->hasMany(MasterItem::class, 'foo-bar');

        $this->assertInstanceOf(HasManyRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation('foo-bar'));
    }

    /**
     * @test
     */
    public function it_can_define_a_has_many_relation_with_the_calling_method_as_fallback_name()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->hasMany(MasterItem::class);

        $this->assertInstanceOf(HasManyRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(__FUNCTION__));
    }

    /**
     * @test
     */
    public function it_can_define_a_morph_to_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->morphTo('foo-bar');

        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation('foo-bar'));
    }

    /**
     * @test
     */
    public function it_can_define_a_morph_to_relation_with_the_calling_method_as_fallback_name()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->morphTo();

        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(__FUNCTION__));
    }

    /**
     * @test
     */
    public function it_can_define_a_morph_to_many_relation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->morphToMany('foo-bar');

        $this->assertInstanceOf(MorphToManyRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation('foo-bar'));
    }

    /**
     * @test
     */
    public function it_can_define_a_morph_to_many_relation_with_the_calling_method_as_fallback_name()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->morphToMany();

        $this->assertInstanceOf(MorphToManyRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(__FUNCTION__));
    }
}
