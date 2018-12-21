<?php

namespace Swis\JsonApi\Client\Relations;

class MorphToManyRelation extends AbstractManyRelation
{
    /**
     * @param string $type
     *
     * @throws \LogicException
     */
    public function setType(string $type)
    {
        throw new \LogicException('Type is not set in a MorphToMany-relationships');
    }

    /**
     * @throws \LogicException
     *
     * @return string
     */
    public function getType(): string
    {
        throw new \LogicException('Type is not set in a MorphToMany-relationship');
    }
}
