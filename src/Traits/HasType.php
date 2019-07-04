<?php

namespace Swis\JsonApi\Client\Traits;

trait HasType
{
    /**
     * @var string
     */
    protected $type;

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function hasType(): bool
    {
        return (bool)$this->type;
    }
}
