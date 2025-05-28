<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class ChildItem extends Item
{
    protected $type = 'child';

    /**
     * @var array<int, string>
     */
    protected $visible = [
        'active',
        'description',
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
}
