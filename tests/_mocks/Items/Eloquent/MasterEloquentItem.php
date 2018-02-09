<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Eloquent;

use Swis\JsonApi\Client\Items\EloquentItem;

class MasterEloquentItem extends EloquentItem
{
    /**
     * @var string
     */
    protected $type = 'master';

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
    ];

    protected $guarded = [];

    public function child()
    {
        return $this->hasOne(ChildEloquentItem::class);
    }
}
