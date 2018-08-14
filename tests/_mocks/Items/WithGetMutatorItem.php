<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class WithGetMutatorItem extends Item
{
    public function getTestAttributeAttribute()
    {
        return 'test';
    }
}
