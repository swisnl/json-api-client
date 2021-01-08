<?php

namespace Swis\JsonApi\Client\Tests\Relations;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Relations\AbstractManyRelation;

class AbstractManyRelationTest extends TestCase
{
    /**
     * @test
     */
    public function itCanAssociateACollectionAndGetTheIncluded()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractManyRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractManyRelation::class);
        $collection = new Collection([new Item()]);

        $mock->associate($collection);

        $this->assertSame($collection, $mock->getIncluded());
    }

    /**
     * @test
     */
    public function itCanDissociateACollection()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractManyRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractManyRelation::class);
        $collection = new Collection([new Item()]);

        $mock->associate($collection);
        $this->assertNotNull($mock->getIncluded());

        $mock->dissociate();

        $this->assertEquals($mock->getIncluded(), new Collection());
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasIncluded()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractManyRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractManyRelation::class);
        $collection = new Collection([new Item()]);

        $this->assertFalse($mock->hasIncluded());
        $mock->associate($collection);

        $this->assertTrue($mock->hasIncluded());
    }

    /**
     * @test
     */
    public function itCanSetAndGetOmitIncluded()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractManyRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractManyRelation::class);

        $this->assertFalse($mock->shouldOmitIncluded());
        $mock->setOmitIncluded(true);

        $this->assertTrue($mock->shouldOmitIncluded());
    }

    /**
     * @test
     */
    public function itCanSortTheIncluded()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractManyRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractManyRelation::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Collection $collectionMock */
        $collectionMock = $this->createMock(Collection::class);

        $collectionMock->expects($this->once())
            ->method('sortBy')
            ->with('foo', SORT_NATURAL, true);

        $mock->associate($collectionMock);

        $mock->sortBy('foo', SORT_NATURAL, true);
    }
}
