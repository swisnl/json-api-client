<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Concerns\HasType;
use Swis\JsonApi\Client\Interfaces\TypedRelationInterface;

class HasManyRelation extends AbstractManyRelation implements TypedRelationInterface
{
    use HasType;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->setType($type);
    }
}
