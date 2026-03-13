<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Item;

class WithRelationshipItem extends Item
{
    protected $type = 'item-with-relationship';

    /**
     * @var array<int, string>
     */
    protected $visible = [
        'test_attribute_1',
        'test_attribute_2',
    ];

    /**
     * @var array<int, string>
     */
    protected $availableRelations = [
        'hasone_relation',
        'hasmany_relation',
        'morphto_relation',
        'morphtomany_relation',
    ];

    /**
     * @return OneRelationInterface<RelatedItem>
     */
    public function hasoneRelation(): OneRelationInterface
    {
        return $this->hasOne(RelatedItem::class);
    }

    /**
     * @return ManyRelationInterface<RelatedItem>
     */
    public function hasmanyRelation(): ManyRelationInterface
    {
        return $this->hasMany(RelatedItem::class);
    }

    /**
     * @return OneRelationInterface<ItemInterface>
     */
    public function morphtoRelation(): OneRelationInterface
    {
        return $this->morphTo();
    }

    /**
     * @return ManyRelationInterface<ItemInterface>
     */
    public function morphtomanyRelation(): ManyRelationInterface
    {
        return $this->morphToMany();
    }
}
