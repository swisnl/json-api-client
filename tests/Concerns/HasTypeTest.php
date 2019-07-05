<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasType;

class HasTypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_and_set_a_type()
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
    public function it_returns_a_boolean_indicating_if_it_has_a_type()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasType $mock */
        $mock = $this->getMockForTrait(HasType::class);

        $this->assertFalse($mock->hasType());

        $mock->setType('foo-bar');

        $this->assertTrue($mock->hasType());
    }
}
