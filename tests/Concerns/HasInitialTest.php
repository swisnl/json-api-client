<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasInitial;

class HasInitialTest extends TestCase
{
    /**
     * @test
     */
    public function itCanGetAndSetInitialAttributes()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasInitial $mock */
        $mock = $this->getMockForTrait(HasInitial::class);
        $initial = ['foo' => 'bar'];

        $mock->setInitial($initial);

        $this->assertSame($initial, $mock->getInitial());
        $this->assertSame($initial['foo'], $mock->getInitial('foo'));
    }

    /**
     * @test
     */
    public function itReturnsABooleanIndicatingIfItHasInitialAttributes()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasInitial $mock */
        $mock = $this->getMockForTrait(HasInitial::class);

        $this->assertFalse($mock->hasInitial());
        $this->assertFalse($mock->hasInitial('foo'));

        $mock->setInitial(['foo' => 'bar']);

        $this->assertTrue($mock->hasInitial());
        $this->assertTrue($mock->hasInitial('foo'));
    }
}
