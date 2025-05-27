<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class WithoutRelationshipsItem extends Item
{
    protected $type = 'item-without-relationships';
}
