<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithGetMutatorItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\WithHiddenItem;

class ItemTest extends AbstractTest
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
        $item = new Item();
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @test
     */
    public function is_shows_type_and_id_and_attributes_in_to_json_api_array()
    {
        $item = new Item($this->attributes);
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
        $item = new Item();
        $item->setType('testType');

        $this->assertEquals('testType', $item->getType());
    }

    /**
     * @test
     */
    public function it_is_new_when_no_id_isset()
    {
        $item = new Item();
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
        $item = new Item();
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
        $item = new Item();

        $item->setId(1234);
        $this->assertEquals(1234, $item->getId());
    }

    /**
     * @test
     */
    public function it_returns_attributes()
    {
        $item = new Item($this->attributes);
        $this->assertEquals($this->attributes, $item->getAttributes());
    }

    /**
     * @test
     */
    public function it_returns_attribute_from_get_mutator()
    {
        $item = new WithGetMutatorItem();

        $this->assertEquals('test', $item->getAttribute('test_attribute'));
    }

    /**
     * @test
     */
    public function it_sets_initial_values()
    {
        $item = new Item();
        $response = $item->setInitial(['testKey' => 'testValue']);

        $this->assertEquals($item, $response);
        $this->assertEquals(['testKey' => 'testValue'], $item->getInitial());
    }

    /**
     * @test
     */
    public function it_uses_initial_values()
    {
        $itemBuilder = new Item();
        $itemBuilder->fill(['testKey' => 1, 'anotherTestKey' => 'someValue']);
        $itemBuilder->setInitial(['testKey' => 9999]);
        $itemBuilder->useInitial();

        $this->assertEquals(['testKey' => 9999, 'anotherTestKey' => 'someValue'], $itemBuilder->getAttributes());
    }

    /**
     * @test
     */
    public function it_adds_unknown_relationships_in_snake_case()
    {
        $item = new Item();
        $item->setRelation('someRelation', (new Item())->setType('type')->setId(1));

        $this->assertTrue($item->hasRelationship('some_relation'));
    }

    /**
     * @test
     */
    public function is_does_not_show_attributes_in_to_json_api_array_when_it_has_no_attributes()
    {
        $item = new WithHiddenItem($this->attributes);
        $item->setType('testType');
        $item->setId(1234);

        $this->assertEquals(
            [
                'type' => 'testType',
                'id'   => 1234,
            ],
            $item->toJsonApiArray()
        );
    }
}
