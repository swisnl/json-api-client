<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class ParentItem extends Item
{
    /**
     * @var string
     */
    protected $type = 'parent';

    /**
     * @var array
     */
    protected $visible = [
        'active',
        'description',
        'child_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'active' => 'bool',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * @var array
     */
    protected $availableRelations = [
        'child',
        'children',
        'morph',
        'morphmany',
        'does_not_exist',
    ];

    public function child()
    {
        return $this->hasOne(ChildItem::class);
    }

    public function children()
    {
        return $this->hasMany(ChildItem::class);
    }

    public function morph()
    {
        return $this->morphTo();
    }

    public function morphmany()
    {
        return $this->morphToMany();
    }

    public function empty()
    {
        return $this->hasOne(ChildItem::class);
    }
}
