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
        'morph',
        'morphmany',
    ];

    public function child()
    {
        return $this->hasOne(ChildJenssegersItem::class);
    }

    public function morph()
    {
        return $this->morphTo();
    }

    public function morphmany()
    {
        return $this->morphToMany();
    }
}
