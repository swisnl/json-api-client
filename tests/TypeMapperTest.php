<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use PHPUnit\Framework\TestCase;
use Swis\JsonApi\Client\Exceptions\TypeMappingException;
use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\TypeMapper;

class TypeMapperTest extends TestCase
{
    /**
     * @test
     */
    public function it_remembers_type_mappings_after_setting()
    {
        $typeMapper = new TypeMapper;
        $typeMapper->setMapping('item', Item::class);

        $this->assertTrue($typeMapper->hasMapping('item'));
        $this->assertInstanceOf(Item::class, $typeMapper->getMapping('item'));
    }

    /**
     * @test
     */
    public function it_forgets_type_mappings_after_removing()
    {
        $typeMapper = new TypeMapper;
        $typeMapper->setMapping('item', Item::class);
        $typeMapper->removeMapping('item');

        $this->assertFalse($typeMapper->hasMapping('item'));
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_mapping_doesnt_exist()
    {
        $this->expectException(TypeMappingException::class);

        $typeMapper = new TypeMapper;
        $typeMapper->getMapping('item');
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_class_doesnt_exist()
    {
        $this->expectException(TypeMappingException::class);
        $this->expectExceptionMessage(sprintf('Class %s not found.', '\Non\Existing\Class'));

        $typeMapper = new TypeMapper;
        $typeMapper->setMapping('item', '\Non\Existing\Class');
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_class_doesnt_implement_iteminterface()
    {
        $this->expectException(TypeMappingException::class);
        $this->expectExceptionMessage(sprintf('Class %s must implement %s.', TypeMapper::class, ItemInterface::class));

        $typeMapper = new TypeMapper;
        $typeMapper->setMapping('item', TypeMapper::class);
    }
}
