<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class WithHiddenItem extends Item
{
    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'testKey',
    ];
}
