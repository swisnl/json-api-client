<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\ItemInterface;
use Swis\JsonApi\Client\Interfaces\OneRelationInterface;

/**
 * @property \Swis\JsonApi\Client\Interfaces\ItemInterface|false|null $data
 * @property \Swis\JsonApi\Client\Interfaces\ItemInterface|false|null $included
 */
abstract class AbstractOneRelation extends AbstractRelation implements OneRelationInterface
{
    /**
     * @return $this
     */
    public function setData(?ItemInterface $data)
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): ?ItemInterface
    {
        return $this->data ?: null;
    }

    /**
     * @return $this
     */
    public function setIncluded(?ItemInterface $included)
    {
        $this->included = $included;

        return $this;
    }

    public function getIncluded(): ?ItemInterface
    {
        return $this->included ?: null;
    }

    /**
     * @return $this
     */
    public function associate(ItemInterface $included)
    {
        return $this->setData($included)
            ->setIncluded($included);
    }

    public function getAssociated(): ?ItemInterface
    {
        if ($this->hasIncluded()) {
            return $this->getIncluded();
        }

        if ($this->hasData()) {
            return $this->getData();
        }

        return null;
    }
}
