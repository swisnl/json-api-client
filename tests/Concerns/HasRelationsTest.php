<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use Illuminate\Support\Str;
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
    public function itCanGetAndSetAnItemAsRelation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Item();

        $mock->setRelation('foo', $data);

        $relation = $mock->getRelation('foo');
        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertTrue($relation->hasIncluded());
        $this->assertSame($data, $relation->getIncluded());
    }

    /**
     * @test
     */
    public function itCanGetAndSetACollectionAsRelation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);
        $data = new Collection();

        $mock->setRelation('foo', $data);

        $relation = $mock->getRelation('foo');
        $this->assertInstanceOf(MorphToManyRelation::class, $relation);
        $this->assertTrue($relation->hasIncluded());
        $this->assertSame($data, $relation->getIncluded());
    }

    /**
     * @test
     */
    public function itCanGetAndSetNullAsRelation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $mock->setRelation('foo', null);

        $relation = $mock->getRelation('foo');
        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertTrue($relation->hasIncluded());
        $this->assertNull($relation->getIncluded());
    }

    /**
     * @test
     */
    public function itDoesNotSetFalseAsRelation()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $mock->setRelation('foo', false);

        $relation = $mock->getRelation('foo');
        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertFalse($relation->hasIncluded());
    }

    /**
     * @test
     */
    public function itSetsTheLinksOnTheRelation()
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
    public function itSetsTheMetaOnTheRelation()
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
    public function itCanGetAllRelations()
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
    public function itCanGetARelationValue()
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
    public function itReturnsNullWhenGettingAnUnexistingRelationValue()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $this->assertNull($mock->getRelationValue('foo'));
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasARelation()
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
    public function itCanUnsetARelation()
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
    public function itCanDefineAHasOneRelation()
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
    public function itCanDefineAHasOneRelationWithTheCallingMethodAsFallbackName()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->hasOne(MasterItem::class);

        $this->assertInstanceOf(HasOneRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(Str::snake(__FUNCTION__)));
    }

    /**
     * @test
     */
    public function itCanDefineAHasManyRelation()
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
    public function itCanDefineAHasManyRelationWithTheCallingMethodAsFallbackName()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->hasMany(MasterItem::class);

        $this->assertInstanceOf(HasManyRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(Str::snake(__FUNCTION__)));
    }

    /**
     * @test
     */
    public function itCanDefineAMorphToRelation()
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
    public function itCanDefineAMorphToRelationWithTheCallingMethodAsFallbackName()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->morphTo();

        $this->assertInstanceOf(MorphToRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(Str::snake(__FUNCTION__)));
    }

    /**
     * @test
     */
    public function itCanDefineAMorphToManyRelation()
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
    public function itCanDefineAMorphToManyRelationWithTheCallingMethodAsFallbackName()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasRelations $mock */
        $mock = $this->getMockForTrait(HasRelations::class);

        $relation = $mock->morphToMany();

        $this->assertInstanceOf(MorphToManyRelation::class, $relation);
        $this->assertSame($relation, $mock->getRelation(Str::snake(__FUNCTION__)));
    }
}
