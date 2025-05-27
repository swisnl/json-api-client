<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Item;

class RelatedItem extends Item
{
    protected $type = 'related-item';

    /**
     * @var array<int, string>
     */
    protected $visible = [
        'test_related_attribute1',
        'test_related_attribute2',
    ];

    /**
     * @var array<int, string>
     */
    protected $availableRelations = [
        'parent_relation',
    ];

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<\Swis\JsonApi\Client\Tests\Mocks\Items\WithRelationshipItem>
     */
    public function parentRelation(): OneRelationInterface
    {
        return $this->hasOne(WithRelationshipItem::class);
    }
}
