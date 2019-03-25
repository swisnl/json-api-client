<?php

namespace Swis\JsonApi\Client\Traits;

use Swis\JsonApi\Client\Meta;

trait HasMeta
{
    /**
     * @var \Swis\JsonApi\Client\Meta|null
     */
    protected $meta;

    /**
     * @param \Swis\JsonApi\Client\Meta|null $meta
     *
     * @return $this
     */
    public function setMeta(Meta $meta = null)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return \Swis\JsonApi\Client\Meta|null
     */
    public function getMeta(): ? Meta
    {
        return $this->meta;
    }
}
