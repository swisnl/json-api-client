<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

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
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<\Swis\JsonApi\Client\Tests\Mocks\Items\RelatedItem>
     */
    public function hasoneRelation(): OneRelationInterface
    {
        return $this->hasOne(RelatedItem::class);
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ManyRelationInterface<\Swis\JsonApi\Client\Tests\Mocks\Items\RelatedItem>
     */
    public function hasmanyRelation(): ManyRelationInterface
    {
        return $this->hasMany(RelatedItem::class);
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<\Swis\JsonApi\Client\Interfaces\ItemInterface>
     */
    public function morphtoRelation(): OneRelationInterface
    {
        return $this->morphTo();
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ManyRelationInterface<\Swis\JsonApi\Client\Interfaces\ItemInterface>
     */
    public function morphtomanyRelation(): ManyRelationInterface
    {
        return $this->morphToMany();
    }
}
