<?php

class ResourceRegistrarTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_registers_the_resource_item_in_type_mappings()
    {
        $typeMapper = new \Swis\JsonApi\TypeMapper();

        $resourceRegistrar = new \Swis\JsonApi\Resource\ResourceRegistrar($typeMapper);
        $item = new MockItem();

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Swis\JsonApi\Resource\Interfaces\ResourceInterface $resource */
        $resource = $this->createMock(\Swis\JsonApi\Resource\Interfaces\ResourceInterface::class);
        $resource->method('getItem')->willReturn($item);

        $resourceRegistrar->registerTypeMapping($resource);

        $this->assertEquals($item, $typeMapper->getMapping($item->getType()));
    }
}
