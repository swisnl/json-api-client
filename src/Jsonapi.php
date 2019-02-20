<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Traits\HasMeta;

class Jsonapi
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
}
