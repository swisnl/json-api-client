<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Meta implements \ArrayAccess, Arrayable, Jsonable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param $key
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     * @param mixed $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR | $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return object
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return (object) $this->toArray();
    }
}
