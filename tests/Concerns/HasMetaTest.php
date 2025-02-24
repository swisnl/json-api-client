<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasMeta;
use Swis\JsonApi\Client\Meta;

class HasMetaTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_and_set_meta()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasMeta $mock */
        $mock = $this->getMockForTrait(HasMeta::class);
        $meta = new Meta([]);

        $mock->setMeta($meta);

        $this->assertSame($meta, $mock->getMeta());
    }
}
