<?php

namespace Swis\JsonApi\Client\Tests\Relations;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Relations\AbstractOneRelation;

class AbstractOneRelationTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_associate_an_item_and_get_the_included()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractOneRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractOneRelation::class);
        $item = new Item();

        $mock->associate($item);

        $this->assertSame($item, $mock->getIncluded());
    }

    /**
     * @test
     */
    public function it_can_dissociate_an_item()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractOneRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractOneRelation::class);
        $item = new Item();

        $mock->associate($item);
        $this->assertNotNull($mock->getIncluded());

        $mock->dissociate();

        $this->assertNull($mock->getIncluded());
    }

    /**
     * @test
     */
    public function it_returns_a_boolean_indicating_if_it_has_included()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractOneRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractOneRelation::class);
        $item = new Item();

        $this->assertFalse($mock->hasIncluded());
        $mock->associate($item);

        $this->assertTrue($mock->hasIncluded());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_omit_included()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractOneRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractOneRelation::class);

        $this->assertFalse($mock->shouldOmitIncluded());
        $mock->setOmitIncluded(true);

        $this->assertTrue($mock->shouldOmitIncluded());
    }
}
