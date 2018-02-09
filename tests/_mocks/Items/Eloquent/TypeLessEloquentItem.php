<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Eloquent;

use Swis\JsonApi\Client\Items\EloquentItem;

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
