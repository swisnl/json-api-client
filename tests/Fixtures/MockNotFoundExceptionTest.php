<?php

namespace Swis\JsonApi\Tests\Fixtures;

use Swis\JsonApi\Fixtures\MockNotFoundException;
use Swis\JsonApi\Tests\AbstractTest;

class MockNotFoundExceptionTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_can_get_possible_paths()
    {
        $paths = ['path1', 'path2'];
        $exception = new MockNotFoundException('message', $paths);

        $this->assertSame($paths, $exception->getPossiblePaths());
    }
}
