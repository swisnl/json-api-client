<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

trait HasId
{
    /**
     * @var string|null
     */
    protected $id;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     *
     * @return static
     */
    public function setId(?string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return isset($this->id);
    }
}
