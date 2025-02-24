<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

trait HasType
{
    /**
     * @var string
     */
    protected $type;

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function hasType(): bool
    {
        return (bool) $this->type;
    }
}
