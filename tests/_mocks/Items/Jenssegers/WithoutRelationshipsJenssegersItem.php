<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Client\Items\JenssegersItem;

class WithoutRelationshipsJenssegersItem extends JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'item-without-relationships';
}
