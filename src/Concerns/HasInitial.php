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
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function getInitial($key = null)
    {
        if (null === $key) {
            return $this->initial;
        }

        return $this->initial[$key];
    }

    /**
     * @param array $initial
     *
     * @return static
     */
    public function setInitial(array $initial)
    {
        $this->initial = $initial;

        return $this;
    }

    /**
     * @param string|null $key
     *
     * @return bool
     */
    public function hasInitial($key = null): bool
    {
        if (null === $key) {
            return !empty($this->initial);
        }

        return isset($this->initial[$key]);
    }
}
