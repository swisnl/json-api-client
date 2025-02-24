<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Concerns\HasLinks;
use Swis\JsonApi\Client\Concerns\HasMeta;

abstract class AbstractRelation
{
    use HasLinks;
    use HasMeta;

    /**
     * @var \Swis\JsonApi\Client\Interfaces\DataInterface|false|null
     */
    protected $data = false;

    /**
     * @var \Swis\JsonApi\Client\Interfaces\DataInterface|false|null
     */
    protected $included = false;

    /**
     * @var bool
     */
    protected $omitIncluded = false;

    public function hasData(): bool
    {
        return $this->data !== false;
    }

    public function hasIncluded(): bool
    {
        return $this->included !== false;
    }

    /**
     * @return $this
     */
    public function dissociate()
    {
        $this->data = null;
        $this->included = null;

        return $this;
    }

    public function hasAssociated(): bool
    {
        return $this->hasData() || $this->hasIncluded();
    }

    /**
     * @return $this
     */
    public function setOmitIncluded(bool $omitIncluded)
    {
        $this->omitIncluded = $omitIncluded;

        return $this;
    }

    public function shouldOmitIncluded(): bool
    {
        return $this->omitIncluded;
    }
}
