<?php

class EloquentItemTest extends AbstractTest
{
    protected $attributes;

    /**
     * ItemTest constructor.
     */
    public function __construct()
    {
        $this->attributes = ['testKey' => 'testValue'];

        parent::__construct();
    }

    /**
     * @test
     */
    public function it_can_instantiate_an_item()
    {
        $item = new \Swis\JsonApi\Items\EloquentItem();
        $this->assertInstanceOf(\Swis\JsonApi\Items\EloquentItem::class, $item);
    }

    /**
     * @test
     */
    public function is_shows_type_and_id_and_attributes_in_to_json_api_array()
    {
        $item = new \Swis\JsonApi\Items\EloquentItem();
        $item->forceFill($this->attributes);
        $item->exists = true;
        $item->setType('testType');
        $item->setId(1234);

        $this->assertEquals(
            [
                'type'       => 'testType',
                'id'         => 1234,
                'attributes' => $this->attributes,
            ],
            $item->toJsonApiArray()
        );
    }

    /**
     * @test
     */
    public function it_gets_and_sets_type()
    {
        $item = new \Swis\JsonApi\Items\EloquentItem();
        $item->setType('testType');

        $this->assertEquals('testType', $item->getType());
    }

    /**
     * @test
     */
    public function it_is_new_when_no_id_isset()
    {
        $this->markTestIncomplete('Fix handling of new vs. existing');
        $item = new \Swis\JsonApi\Items\EloquentItem();
        $item->setType('testType');
        $item->setId(1);

        $this->assertFalse($item->isNew());
        $item->setId(null);
        $this->assertTrue($item->isNew());
    }

    /**
     * @test
     */
    public function it_returns_has_id_when_id_isset()
    {
        $item = new \Swis\JsonApi\Items\EloquentItem();
        $item->setType('testType');
        $this->assertFalse($item->hasId());

        $item->setId(1);
        $this->assertTrue($item->hasId());
    }

    /**
     * @test
     */
    public function it_returns_id_when_id_isset()
    {
        $item = new \Swis\JsonApi\Items\EloquentItem();

        $item->setId(1234);
        $this->assertEquals(1234, $item->getId());
    }

    /**
     * @test
     */
    public function it_returns_attributes()
    {
        $item = new \Swis\JsonApi\Items\EloquentItem();
        $item->forceFill($this->attributes);
        $this->assertEquals($this->attributes, $item->getAttributes());
    }
}
