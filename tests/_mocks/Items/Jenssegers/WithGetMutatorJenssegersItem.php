<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Client\Items\JenssegersItem;

class WithGetMutatorJenssegersItem extends JenssegersItem
{
    public function getTestAttributeAttribute()
    {
        return 'test';
    }
}
