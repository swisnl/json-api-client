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
    public function it_can_associate_a_collection_and_get_the_included()
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
    public function it_can_dissociate_a_collection()
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
    public function it_returns_a_boolean_indicating_if_it_has_included()
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
    public function it_can_set_and_get_omit_included()
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
    public function it_can_sort_the_included()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractManyRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractManyRelation::class);
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Collection $collectionMock */
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethods(['isNotEmpty', 'sortBy'])
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('isNotEmpty')
            ->willReturn(true);
        $collectionMock->expects($this->once())
            ->method('sortBy')
            ->with('foo', SORT_NATURAL, true);

        $mock->associate($collectionMock);

        $mock->sortBy('foo', SORT_NATURAL, true);
    }
}
