<?php

class ChildEloquentItem extends \Swis\JsonApi\Items\EloquentItem
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

    protected $guarded = [];
}
