<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Swis\JsonApi\Client\Concerns\HasMeta;

class Jsonapi implements \JsonSerializable, Arrayable, Jsonable
{
    use HasMeta;

    /**
     * @var string|null
     */
    protected $version;

    public function __construct(?string $version = null, ?Meta $meta = null)
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
