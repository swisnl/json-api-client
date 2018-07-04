<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Client\Items\JenssegersItem;

class WithHiddenJenssegersItem extends JenssegersItem
{
    protected $hidden = [
        'testKey',
    ];
}
