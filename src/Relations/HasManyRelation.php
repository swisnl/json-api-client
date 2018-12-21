<?php

namespace Swis\JsonApi\Client\Relations;

class HasManyRelation extends AbstractManyRelation
{
    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
