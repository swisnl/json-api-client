<?php

namespace Swis\JsonApi\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Items\JenssegersItem;

class WithGetMutatorJenssegersItem extends JenssegersItem
{
    public function getTestAttributeAttribute()
    {
        return 'test';
    }
}
