<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class WithoutRelationshipsItem extends Item
{
    /**
     * @var string
     */
    protected $type = 'item-without-relationships';
}
