<?php

namespace Swis\JsonApi\Client\Tests\Mocks\ResourceRegistrar;

use Swis\JsonApi\Client\Items\JenssegersItem;

class MockItem extends JenssegersItem
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
