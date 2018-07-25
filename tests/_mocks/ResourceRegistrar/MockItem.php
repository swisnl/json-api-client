<?php

namespace Swis\JsonApi\Client\Tests\Mocks\ResourceRegistrar;

use Swis\JsonApi\Client\Item;

class MockItem extends Item
{
    /**
     * @var string
     */
    protected $type = 'test-resource-item';

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

    /**
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];
}
