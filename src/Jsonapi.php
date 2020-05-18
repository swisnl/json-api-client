<?php

namespace Swis\JsonApi\Client;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Swis\JsonApi\Client\Concerns\HasMeta;

class Jsonapi implements Arrayable, Jsonable, JsonSerializable
{
    use HasMeta;

    /**
     * @var string|null
     */
    protected $version;

    /**
     * @param string|null                    $version
     * @param \Swis\JsonApi\Client\Meta|null $meta
     */
    public function __construct(string $version = null, Meta $meta = null)
    {
        $this->version = $version;
        $this->meta = $meta;
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        if ($this->getVersion() !== null) {
            $array['version'] = $this->getVersion();
        }

        if ($this->getMeta() !== null) {
            $array['meta'] = $this->getMeta()->toArray();
        }

        return $array;
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
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * {@inheritdoc}
     *
     * @return object
     */
    public function jsonSerialize()
    {
        return (object) $this->toArray();
    }
}
