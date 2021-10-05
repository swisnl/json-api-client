<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Relations;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Relations\AbstractOneRelation;

class AbstractOneRelationTest extends TestCase
{
    /**
     * @test
     */
    public function itCanAssociateAnItemAndGetTheIncluded()
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
    public function itCanDissociateAnItem()
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
    public function itReturnsABooleanIndicatingIfItHasIncluded()
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
    public function itCanSetAndGetOmitIncluded()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Relations\AbstractOneRelation $mock */
        $mock = $this->getMockForAbstractClass(AbstractOneRelation::class);

        $this->assertFalse($mock->shouldOmitIncluded());
        $mock->setOmitIncluded(true);

        $this->assertTrue($mock->shouldOmitIncluded());
    }
}
