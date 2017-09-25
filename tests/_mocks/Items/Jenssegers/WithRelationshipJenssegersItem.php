<?php

class WithRelationshipJenssegersItem extends \Swis\JsonApi\Items\JenssegersItem
{
    /**
     * @var string
     */
    protected $type = 'item-with-relationship';

    /**
     * @var array
     */
    protected $visible = [
        'test_attribute_1',
        'test_attribute_2',
    ];

    /**
     * @var array
     */
    protected $availableRelations = [
        'hasone_relation',
        'hasmany_relation',
        'morphto_relation',
    ];

    public function hasoneRelation()
    {
        return $this->hasOne(RelatedJenssegersItem::class);
    }

    public function hasmanyRelation()
    {
        return $this->hasMany(RelatedJenssegersItem::class);
    }

    public function morphtoRelation()
    {
        return $this->morphTo();
    }
}
