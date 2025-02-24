<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

use Swis\JsonApi\Client\Meta;

trait HasMeta
{
    /**
     * @var \Swis\JsonApi\Client\Meta|null
     */
    protected $meta;

    public function getMeta(): ?Meta
    {
        return $this->meta;
    }

    /**
     * @return $this
     */
    public function setMeta(?Meta $meta)
    {
        $this->meta = $meta;

        return $this;
    }
}
