<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class WithRelationshipItem extends Item
{
    /**
     * @var string
     */
    protected $type = 'item-with-relationship';

    /**
     * @var array
     */
    protected $visible = [
        'test_attribute_1',
        'test_attribute_2',
    ];

    /**
     * @var array
     */
    protected $availableRelations = [
        'hasone_relation',
        'hasmany_relation',
        'morphto_relation',
        'morphtomany_relation',
    ];

    public function hasoneRelation()
    {
        return $this->hasOne(RelatedItem::class);
    }

    public function hasmanyRelation()
    {
        return $this->hasMany(RelatedItem::class);
    }

    public function morphtoRelation()
    {
        return $this->morphTo();
    }

    public function morphtomanyRelation()
    {
        return $this->morphToMany();
    }
}
