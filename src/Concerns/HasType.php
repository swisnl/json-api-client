<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

trait HasType
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

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
     * @return bool
     */
    public function hasType(): bool
    {
        return (bool) $this->type;
    }
}
