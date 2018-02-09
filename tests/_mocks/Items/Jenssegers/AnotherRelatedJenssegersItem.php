<?php

namespace Swis\JsonApi\Client\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Client\Items\JenssegersItem;

class AnotherRelatedJenssegersItem extends JenssegersItem
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
