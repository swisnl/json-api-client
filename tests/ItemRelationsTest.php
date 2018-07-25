<?php

namespace Swis\JsonApi\Client\Tests;

use Swis\JsonApi\Client\Item;
use Swis\JsonApi\Client\Relations\HasOneRelation;
use Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem;
use Swis\JsonApi\Client\Tests\Mocks\Items\MasterItem;

class ItemRelationsTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_has_relationships_when_added()
    {
        $masterItem = new MasterItem();
        $this->assertInstanceOf(HasOneRelation::class, $masterItem->child());

        $childItem = new ChildItem();
        $childItem->setId(1);
        $this->assertEquals(1, $childItem->getId());
        $masterItem->child()->associate($childItem);
        $masterItem->child()->setId(1);

        $this->assertEquals(1, $masterItem->child()->getId());
    }

    /**
     * @test
     */
    public function it_can_check_for_relations()
    {
        $item = new Item();
        $this->assertFalse($item->hasRelationship('test'));

        $masterItem = new MasterItem();
        $this->assertFalse($masterItem->hasRelationship('child'));

        $childItem = new ChildItem();
        $childItem->setId(1);
        $masterItem->child()->associate($childItem);
        $this->assertTrue($masterItem->hasRelationship('child'));
    }

    /**
     * @test
     */
    public function it_can_get_all_relations()
    {
        $masterItem = new MasterItem();
        $childItem = new ChildItem();
        $childItem->setId(1);
        $masterItem->child()->setId(1);
        $masterItem->child()->associate($childItem);

        $relations = $masterItem->getRelationships();

        $this->assertArraySubset([
            'child' => [
                'data' => [
                    'type' => 'child',
                    'id'   => 1,
                ],
            ],
        ], $relations);
    }
}
