<?php

namespace Swis\JsonApi\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Items\JenssegersItem;

class WithRelationshipJenssegersItem extends JenssegersItem
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
        'morphtomany_relation',
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

    public function morphtomanyRelation()
    {
        return $this->morphToMany();
    }
}
