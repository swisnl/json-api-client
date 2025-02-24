<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

trait HasId
{
    /**
     * @var string|null
     */
    protected $id;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return static
     */
    public function setId(?string $id)
    {
        $this->id = $id;

        return $this;
    }

    public function hasId(): bool
    {
        return isset($this->id);
    }
}
