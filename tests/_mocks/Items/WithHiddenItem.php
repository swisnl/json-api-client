<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class WithHiddenItem extends Item
{
    protected $hidden = [
        'testKey',
    ];
}
