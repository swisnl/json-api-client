<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\RelationInterface;

abstract class AbstractRelation implements RelationInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $omitIncluded = false;

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param bool $omitIncluded
     *
     * @return $this
     */
    public function setOmitIncluded(bool $omitIncluded)
    {
        $this->omitIncluded = $omitIncluded;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldOmitIncluded(): bool
    {
        return $this->omitIncluded;
    }
}
