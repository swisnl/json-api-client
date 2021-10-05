<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class RelatedItem extends Item
{
    /**
     * @var string
     */
    protected $type = 'related-item';

    /**
     * @var array
     */
    protected $visible = [
        'test_related_attribute1',
        'test_related_attribute2',
    ];

    /**
     * @var array
     */
    protected $availableRelations = [
        'parent_relation',
    ];

    public function parentRelation()
    {
        return $this->hasOne(WithRelationshipItem::class);
    }
}
