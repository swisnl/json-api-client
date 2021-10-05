<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests\Mocks\Items;

use Swis\JsonApi\Client\Item;

class AnotherRelatedItem extends Item
{
    /**
     * @var string
     */
    protected $type = 'another-related-item';

    /**
     * @var array
     */
    protected $visible = [
        'test_related_attribute1',
        'test_related_attribute2',
    ];
}
