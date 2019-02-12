<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\Meta as JsonApiMeta;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;
use Swis\JsonApi\Client\Meta;

class LinksParser
{
    /**
     * @param array $links
     *
     * @return \Swis\JsonApi\Client\Links
     */
    public function parse(array $links)
    {
        return new Links(
            array_map(
                function ($link) {
                    return $this->buildLink($link);
                },
                $links
            )
        );
    }

    /**
     * @param \Art4\JsonApiClient\Link|string $link
     *
     * @return \Swis\JsonApi\Client\Link
     */
    private function buildLink($link): Link
    {
        if (is_string($link)) {
            return new Link($link);
        }

        return new Link($link->get('href'), $link->has('meta') ? $this->buildMeta($link->get('meta')) : null);
    }

    /**
     * @param \Art4\JsonApiClient\Meta $meta
     *
     * @return \Swis\JsonApi\Client\Meta
     */
    private function buildMeta(JsonApiMeta $meta): Meta
    {
        return new Meta($meta->asArray(true));
    }
}
