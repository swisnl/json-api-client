<?php

namespace Swis\JsonApi\Client\Traits;

use Swis\JsonApi\Client\Links;

trait HasLinks
{
    /**
     * @var \Swis\JsonApi\Client\Links|null
     */
    protected $links;

    /**
     * @param \Swis\JsonApi\Client\Links|null $links
     *
     * @return $this
     */
    public function setLinks(? Links $links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return \Swis\JsonApi\Client\Links|null
     */
    public function getLinks(): ? Links
    {
        return $this->links;
    }
}
