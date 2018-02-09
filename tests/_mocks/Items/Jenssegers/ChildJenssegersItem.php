<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Client\Items\JenssegersItem;

class ChildJenssegersItem extends JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'child';

    /**
     * @var array
     */
    protected $visible = [
        'active',
        'description',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'active' => 'bool',
    ];

    protected $attributes = [
        'active' => true,
    ];
}
