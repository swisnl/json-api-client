<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasId;

class HasIdTest extends TestCase
{
    /**
     * @test
     */
    public function itCanGetAndSetAnId()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasId $mock */
        $mock = $this->getMockForTrait(HasId::class);
        $id = '123';

        $mock->setId($id);

        $this->assertSame($id, $mock->getId());
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasAnId()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasId $mock */
        $mock = $this->getMockForTrait(HasId::class);

        $this->assertFalse($mock->hasId());

        $mock->setId('123');

        $this->assertTrue($mock->hasId());
    }
}
