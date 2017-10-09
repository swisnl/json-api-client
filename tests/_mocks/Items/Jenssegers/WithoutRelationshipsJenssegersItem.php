<?php

namespace Swis\JsonApi\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Items\JenssegersItem;

class WithoutRelationshipsJenssegersItem extends JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'item-without-relationships';
}
