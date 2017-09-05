<?php

class ItemHydratorTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_hydrates_items_without_relationships()
    {
        $data = [
            'testattribute1' => 'test',
            'testattribute2' => 'test2',
        ];

        $item = new \Swis\JsonApi\Items\JenssegersItem();

        $item = $this->getItemHydrator()->hydrate($item, $data);

        $this->assertEquals($data, $item->getAttributes());
    }

    /**
     * @return \Swis\JsonApi\ItemHydrator
     */
    private function getItemHydrator()
    {
        $typeMapper = new \Swis\JsonApi\TypeMapper();
        $typeMapper->setMapping('hydratedItem', \Swis\JsonApi\Items\JenssegersItem::class);

        $typeMapper->setMapping('related-item', RelatedJenssegersItem::class);

        return new \Swis\JsonApi\ItemHydrator($typeMapper);
    }

    /**
     * @test
     */
    public function it_hydrates_items_with_hasone_relationships()
    {
        $data = [
            'testattribute1'  => 'test',
            'testattribute2'  => 'test2',
            'hasone_relation' => 1,
        ];

        $item = new WithRelationshipJenssegersItem();
        $item = $this->getItemHydrator()->hydrate($item, $data);

        $hasOne = $item->getRelationship('hasone_relation');

        $this->assertInstanceOf(
            \Swis\JsonApi\Relations\HasOneRelation::class,
            $hasOne
        );

        $this->assertEquals($data['testattribute1'], $item->getAttribute('testattribute1'));
        $this->assertEquals($data['testattribute2'], $item->getAttribute('testattribute2'));

        $this->assertEquals($data['hasone_relation'], $hasOne->getId());
        $this->assertEquals('related-item', $hasOne->getType());
    }

    /**
     * @test
     */
    public function it_hydrates_items_with_hasmany_relationships()
    {
        $data = [
            'testattribute1'   => 'test',
            'testattribute2'   => 'test2',
            'hasmany_relation' => [
                [
                    'id'                      => 1,
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
                [
                    'id'                      => 2,
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
            ],
        ];

        $item = new WithRelationshipJenssegersItem();

        $item = $this->getItemHydrator()->hydrate($item, $data);
        $hasMany = $item->getRelationship('hasmany_relation');

        $this->assertInstanceOf(
            \Swis\JsonApi\Relations\HasManyRelation::class,
            $hasMany
        );

        $this->assertInstanceOf(\Swis\JsonApi\Collection::class, $hasMany->getIncluded());
        $this->assertCount(2, $hasMany->getIncluded());

        $expected = [
            [
                'id'         => 1,
                'type'       => 'related-item',
                'attributes' => [
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
            ],
            [
                'id'         => 2,
                'type'       => 'related-item',
                'attributes' => [
                    'test_related_attribute1' => 'test',
                    'test_related_attribute2' => 'test2',
                ],
            ],
        ];

        $this->assertEquals($expected, $hasMany->getIncluded()->toJsonApiArray());
    }
}
