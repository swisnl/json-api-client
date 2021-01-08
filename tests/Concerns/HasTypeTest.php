<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasType;

class HasTypeTest extends TestCase
{
    /**
     * @test
     */
    public function itCanGetAndSetAType()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasType $mock */
        $mock = $this->getMockForTrait(HasType::class);
        $type = 'foo-bar';

        $mock->setType($type);

        $this->assertSame($type, $mock->getType());
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasAType()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasType $mock */
        $mock = $this->getMockForTrait(HasType::class);

        $this->assertFalse($mock->hasType());

        $mock->setType('foo-bar');

        $this->assertTrue($mock->hasType());
    }
}
