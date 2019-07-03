<?php

namespace Swis\JsonApi\Client\Tests;

use InvalidArgumentException;
use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\TypeMapper;

class TypeMapperTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_remembers_type_mappings_after_setting()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', Item::class);

        static::assertTrue($typeMapper->hasMapping('item'));
        static::assertInstanceOf(Item::class, $typeMapper->getMapping('item'));
    }

    /**
     * @test
     */
    public function it_forgets_type_mappings_after_removing()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', Item::class);
        $typeMapper->removeMapping('item');

        static::assertFalse($typeMapper->hasMapping('item'));
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_mapping_doesnt_exist()
    {
        static::expectException(InvalidArgumentException::class);

        $typeMapper = new TypeMapper();
        $typeMapper->getMapping('item');
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_class_doesnt_exist()
    {
        static::expectException(InvalidArgumentException::class);

        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', '\Non\Existing\Class');
    }
}
