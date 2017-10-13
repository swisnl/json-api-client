<?php

namespace Swis\JsonApi\Tests\Mocks\Items\Jenssegers;

use Swis\JsonApi\Items\JenssegersItem;

class RelatedJenssegersItem extends JenssegersItem
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

    /**
     * @var array
     */
    protected $availableRelations = [
        'parent_relation',
    ];

    public function parentRelation()
    {
        return $this->hasOne(WithRelationshipJenssegersItem::class);
    }
}
