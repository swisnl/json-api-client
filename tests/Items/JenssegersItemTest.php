<?php

class JenssegersItemTest extends AbstractTest
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
        $item = new \Swis\JsonApi\Items\JenssegersItem();
        $this->assertInstanceOf(\Swis\JsonApi\Items\JenssegersItem::class, $item);
    }

    /**
     * @test
     */
    public function is_shows_type_and_id_and_attributes_in_to_json_api_array()
    {
        $item = new \Swis\JsonApi\Items\JenssegersItem($this->attributes);
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
        $item = new \Swis\JsonApi\Items\JenssegersItem();
        $item->setType('testType');

        $this->assertEquals('testType', $item->getType());
    }

    /**
     * @test
     */
    public function it_is_new_when_no_id_isset()
    {
        $item = new \Swis\JsonApi\Items\JenssegersItem();
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
        $item = new \Swis\JsonApi\Items\JenssegersItem();
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
        $item = new \Swis\JsonApi\Items\JenssegersItem();

        $item->setId(1234);
        $this->assertEquals(1234, $item->getId());
    }

    /**
     * @test
     */
    public function it_returns_attributes()
    {
        $item = new \Swis\JsonApi\Items\JenssegersItem($this->attributes);
        $this->assertEquals($this->attributes, $item->getAttributes());
    }

    /**
     * @test
     */
    public function it_sets_initial_values()
    {
        $item = new \Swis\JsonApi\Items\JenssegersItem();
        $response = $item->setInitial(['testKey' => 'testValue']);

        $this->assertEquals($item, $response);
        $this->assertEquals(['testKey' => 'testValue'], $item->getInitial());
    }

    /**
     * @test
     */
    public function it_uses_initial_values()
    {
        $itemBuilder = new \Swis\JsonApi\Items\JenssegersItem();
        $itemBuilder->fill(['testKey' => 1, 'anotherTestKey' => 'someValue']);
        $itemBuilder->setInitial(['testKey' => 9999]);
        $itemBuilder->useInitial();

        $this->assertEquals(['testKey' => 9999, 'anotherTestKey' => 'someValue'], $itemBuilder->getAttributes());
    }
}
