<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

trait HasInitial
{
    /**
     * @var array
     */
    protected $initial = [];

    /**
     * @param  string|null  $key
     * @return array|mixed
     */
    public function getInitial($key = null)
    {
        if ($key === null) {
            return $this->initial;
        }

        return $this->initial[$key];
    }

    /**
     * @return static
     */
    public function setInitial(array $initial)
    {
        $this->initial = $initial;

        return $this;
    }

    /**
     * @param  string|null  $key
     */
    public function hasInitial($key = null): bool
    {
        if ($key === null) {
            return ! empty($this->initial);
        }

        return isset($this->initial[$key]);
    }
}
