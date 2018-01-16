<?php

namespace Swis\JsonApi\Tests\Mocks\Items\Eloquent;

use Swis\JsonApi\Items\EloquentItem;

class TypeLessEloquentItem extends EloquentItem
{
    /**
     * @var array
     */
    protected $visible = [
        'active',
        'description',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'active' => 'bool',
    ];

    protected $attributes = [
        'active' => true,
    ];

    protected $guarded = [];
}
