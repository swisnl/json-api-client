<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Concerns\HasType;
use Swis\JsonApi\Client\Interfaces\TypedRelationInterface;

class HasOneRelation extends AbstractOneRelation implements TypedRelationInterface
{
    use HasType;

    public function __construct(string $type)
    {
        $this->setType($type);
    }
}
