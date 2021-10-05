<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;

/**
 * @property \Swis\JsonApi\Client\Interfaces\ItemInterface|false|null $included
 */
abstract class AbstractOneRelation extends AbstractRelation implements OneRelationInterface
{
    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $included
     *
     * @return $this
     */
    public function associate(ItemInterface $included)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemInterface|null
     */
    public function getIncluded(): ?ItemInterface
    {
        return $this->included ?: null;
    }
}
