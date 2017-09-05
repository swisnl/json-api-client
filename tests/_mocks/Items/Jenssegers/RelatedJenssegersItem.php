<?php

class RelatedJenssegersItem extends \Swis\JsonApi\Items\JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'related-item';

    /**
     * @var array
     */
    protected $visible = [
        'test_related_attribute1',
        'test_related_attribute2',
    ];
}
