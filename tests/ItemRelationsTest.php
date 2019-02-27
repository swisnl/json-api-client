<?php

namespace Swis\JsonApi\Client\Tests;

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
        $masterItem->child()->associate($childItem);

        $relations = $masterItem->getRelationships();

        $this->assertSame([
            'child' => [
                'data' => [
                    'type' => 'child',
                    'id'   => 1,
                ],
            ],
        ], $relations);
    }
}
