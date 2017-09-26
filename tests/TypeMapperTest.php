<?php

namespace Swis\JsonApi\Tests;

use InvalidArgumentException;
use Swis\JsonApi\Items\JenssegersItem;
use Swis\JsonApi\TypeMapper;

class TypeMapperTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_remembers_type_mappings_after_setting()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', JenssegersItem::class);

        static::assertTrue($typeMapper->hasMapping('item'));
        static::assertInstanceOf(JenssegersItem::class, $typeMapper->getMapping('item'));
    }

    /**
     * @test
     */
    public function it_forgets_type_mappings_after_removing()
    {
        $typeMapper = new TypeMapper();
        $typeMapper->setMapping('item', JenssegersItem::class);
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
        $typeMapper->getMapping('item');
    }
}
