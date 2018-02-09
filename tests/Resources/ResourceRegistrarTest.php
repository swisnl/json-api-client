<?php

namespace Swis\JsonApi\Client\Tests\Resources;

use Swis\JsonApi\Client\Resource\Interfaces\ResourceInterface;
use Swis\JsonApi\Client\Resource\ResourceRegistrar;
use Swis\JsonApi\Client\Tests\AbstractTest;
use Swis\JsonApi\Client\Tests\Mocks\ResourceRegistrar\MockItem;
use Swis\JsonApi\Client\TypeMapper;

class ResourceRegistrarTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_registers_the_resource_item_in_type_mappings()
    {
        $typeMapper = new TypeMapper();

        $resourceRegistrar = new ResourceRegistrar($typeMapper);
        $item = new MockItem();

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Swis\JsonApi\Client\Resource\Interfaces\ResourceInterface $resource */
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getItem')->willReturn($item);

        $resourceRegistrar->registerTypeMapping($resource);

        $this->assertEquals($item, $typeMapper->getMapping($item->getType()));
    }
}
