<?php

class MockItem extends \Swis\JsonApi\Items\JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'test-resource-item';

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

    /**
     * @var array
     */
    protected $attributes = [
        'active' => true,
    ];
}
