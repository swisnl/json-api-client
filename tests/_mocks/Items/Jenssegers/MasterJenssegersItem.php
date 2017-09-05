<?php

class MasterJenssegersItem extends \Swis\JsonApi\Items\JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'master';

    /**
     * @var array
     */
    protected $visible = [
        'active',
        'description',
        'child_id',
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

    /**
     * @var array
     */
    protected $availableRelations = [
        'child',
    ];

    public function child()
    {
        return $this->hasOne(ChildJenssegersItem::class);
    }
}
