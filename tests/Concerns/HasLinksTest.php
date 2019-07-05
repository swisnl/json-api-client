<?php

namespace Swis\JsonApi\Client\Tests\Concerns;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Concerns\HasLinks;
use Swis\JsonApi\Client\Links;

class HasLinksTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_get_and_set_links()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Swis\JsonApi\Client\Concerns\HasLinks $mock */
        $mock = $this->getMockForTrait(HasLinks::class);
        $links = new Links([]);

        $mock->setLinks($links);

        $this->assertSame($links, $mock->getLinks());
    }
}
