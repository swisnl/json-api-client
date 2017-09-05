<?php

class TypeMapperTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_remembers_type_mappings_after_setting()
    {
        $typeMapper = new \Swis\JsonApi\TypeMapper();
        $typeMapper->setMapping('item', \Swis\JsonApi\Items\JenssegersItem::class);

        static::assertTrue($typeMapper->hasMapping('item'));
        static::assertInstanceOf(\Swis\JsonApi\Items\JenssegersItem::class, $typeMapper->getMapping('item'));
    }

    /**
     * @test
     */
    public function it_forgets_type_mappings_after_removing()
    {
        $typeMapper = new \Swis\JsonApi\TypeMapper();
        $typeMapper->setMapping('item', \Swis\JsonApi\Items\JenssegersItem::class);
        $typeMapper->removeMapping('item');

        static::assertFalse($typeMapper->hasMapping('item'));
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_mapping_doesnt_exist()
    {
        static::expectException(InvalidArgumentException::class);

        $typeMapper = new \Swis\JsonApi\TypeMapper();
        $typeMapper->getMapping('item');
    }

    /**
     * @test
     */
    public function it_throws_an_invalidargumentexception_when_class_doesnt_exist()
    {
        static::expectException(InvalidArgumentException::class);

        $typeMapper = new \Swis\JsonApi\TypeMapper();
        $typeMapper->setMapping('item', '\Non\Existing\Class');
        $typeMapper->getMapping('item');
    }
}
