<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Exceptions\TypeMappingException;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\TypeMapper;

class TypeMapperTest extends AbstractTest
{
    /**
     * @test
     */
    public function itRemembersTypeMappingsAfterSetting()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', Item::class);

        $this->assertTrue($typeMapper->hasMapping('item'));
        $this->assertInstanceOf(Item::class, $typeMapper->getMapping('item'));
    }

    /**
     * @test
     */
    public function itForgetsTypeMappingsAfterRemoving()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', Item::class);
        $typeMapper->removeMapping('item');

        $this->assertFalse($typeMapper->hasMapping('item'));
    }

    /**
     * @test
     */
    public function itThrowsAnInvalidargumentexceptionWhenMappingDoesntExist()
    {
        $this->expectException(TypeMappingException::class);

        $typeMapper = new TypeMapper();
        $typeMapper->getMapping('item');
    }

    /**
     * @test
     */
    public function itThrowsAnInvalidargumentexceptionWhenClassDoesntExist()
    {
        $this->expectException(TypeMappingException::class);
        $this->expectExceptionMessage(sprintf('Class %s not found.', '\Non\Existing\Class'));

        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', '\Non\Existing\Class');
    }

    /**
     * @test
     */
    public function itThrowsAnInvalidargumentexceptionWhenClassDoesntImplementIteminterface()
    {
        $this->expectException(TypeMappingException::class);
        $this->expectExceptionMessage(sprintf('Class %s must implement %s.', TypeMapper::class, ItemInterface::class));

        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', TypeMapper::class);
    }
}
