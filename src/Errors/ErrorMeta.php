<?php

namespace Swis\JsonApi\Errors;

class ErrorMeta
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_has($this->data, $key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return array_get($this->data, $key);
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->data;
    }
}
