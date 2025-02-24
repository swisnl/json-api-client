<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Links implements \ArrayAccess, \JsonSerializable, Arrayable, Jsonable
{
    /**
     * @var \Swis\JsonApi\Client\Link[]
     */
    protected $links = [];

    /**
     * @param  \Swis\JsonApi\Client\Link[]  $links
     */
    public function __construct(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed  $offset
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->links[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed  $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->links[$offset] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->links[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed  $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->links[$offset]);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(
            static fn (?Link $link) => $link ? $link->toArray() : null,
            $this->links
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param  int  $options
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
