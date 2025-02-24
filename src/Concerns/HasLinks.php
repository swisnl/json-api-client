<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Concerns;

use Swis\JsonApi\Client\Links;

trait HasLinks
{
    /**
     * @var \Swis\JsonApi\Client\Links|null
     */
    protected $links;

    public function getLinks(): ?Links
    {
        return $this->links;
    }

    /**
     * @return $this
     */
    public function setLinks(?Links $links)
    {
        $this->links = $links;

        return $this;
    }
}
