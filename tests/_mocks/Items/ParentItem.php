<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Interfaces\ManyRelationInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;
use Swis\JsonApi\Client\Item;

class ParentItem extends Item
{
    protected $type = 'parent';

    /**
     * @var array<int, string>
     */
    protected $visible = [
        'active',
        'description',
        'child_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'bool',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'active' => true,
    ];

    /**
     * @var array<int, string>
     */
    protected $availableRelations = [
        'child',
        'children',
        'morph',
        'morphmany',
        'does_not_exist',
    ];

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<\Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>
     */
    public function child(): OneRelationInterface
    {
        return $this->hasOne(ChildItem::class);
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ManyRelationInterface<\Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>
     */
    public function children(): ManyRelationInterface
    {
        return $this->hasMany(ChildItem::class);
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<\Swis\JsonApi\Client\Interfaces\ItemInterface>
     */
    public function morph(): OneRelationInterface
    {
        return $this->morphTo();
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ManyRelationInterface<\Swis\JsonApi\Client\Interfaces\ItemInterface>
     */
    public function morphmany(): ManyRelationInterface
    {
        return $this->morphToMany();
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\OneRelationInterface<\Swis\JsonApi\Client\Tests\Mocks\Items\ChildItem>
     */
    public function empty(): OneRelationInterface
    {
        return $this->hasOne(ChildItem::class);
    }
}
