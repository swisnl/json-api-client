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

    /**
     * @return bool
     */
    public function hasData(): bool
    {
        return $this->data !== false;
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function hasAssociated(): bool
    {
        return $this->hasData() || $this->hasIncluded();
    }

    /**
     * @param bool $omitIncluded
     *
     * @return $this
     */
    public function setOmitIncluded(bool $omitIncluded)
    {
        $this->omitIncluded = $omitIncluded;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldOmitIncluded(): bool
    {
        return $this->omitIncluded;
    }
}
