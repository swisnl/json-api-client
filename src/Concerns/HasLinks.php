<?php

namespace Swis\JsonApi\Client\Concerns;

use Swis\JsonApi\Client\Links;

trait HasLinks
{
    /**
     * @var \Swis\JsonApi\Client\Links|null
     */
    protected $links;

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ?Links
    {
        return $this->links;
    }

    /**
     * @param \Swis\JsonApi\Client\Links|null $links
     *
     * @return $this
     */
    public function setLinks(?Links $links)
    {
        $this->links = $links;

        return $this;
    }
}
