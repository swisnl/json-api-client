<?php

namespace Swis\JsonApi\Tests\Mocks\ResourceRegistrar;

use Swis\JsonApi\Items\JenssegersItem;

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
