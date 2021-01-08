<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasMeta;
use Swis\JsonApi\Client\Meta;

class HasMetaTest extends TestCase
{
    /**
     * @test
     */
    public function itCanGetAndSetMeta()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasMeta $mock */
        $mock = $this->getMockForTrait(HasMeta::class);
        $meta = new Meta([]);

        $mock->setMeta($meta);

        $this->assertSame($meta, $mock->getMeta());
    }
}
