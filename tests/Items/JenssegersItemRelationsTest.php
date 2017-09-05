<?php

class JenssegersItemRelationsTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_has_relationships_when_added()
    {
        $masterItem = new MasterJenssegersItem();
        $this->assertInstanceOf(\Swis\JsonApi\Relations\HasOneRelation::class, $masterItem->child());

        $childItem = new ChildJenssegersItem();
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
        $item = new \Swis\JsonApi\Items\JenssegersItem();
        $this->assertFalse($item->hasRelationship('test'));

        $masterItem = new MasterJenssegersItem();
        $this->assertFalse($masterItem->hasRelationship('child'));

        $childItem = new ChildJenssegersItem();
        $childItem->setId(1);
        $masterItem->child()->associate($childItem);
        $this->assertTrue($masterItem->hasRelationship('child'));
    }

    /**
     * @test
     */
    public function it_can_get_all_relations()
    {
        $masterItem = new MasterJenssegersItem();
        $childItem = new ChildJenssegersItem();
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
