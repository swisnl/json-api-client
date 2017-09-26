<?php

class AnotherRelatedJenssegersItem extends \Swis\JsonApi\Items\JenssegersItem
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
