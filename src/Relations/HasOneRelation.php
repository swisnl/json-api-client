<?php

namespace Swis\JsonApi\Client\Relations;

class HasOneRelation extends AbstractOneRelation
{
    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
