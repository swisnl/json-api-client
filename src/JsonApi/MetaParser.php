<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\Meta as JsonApiMeta;
use Swis\JsonApi\Client\Meta;

/**
 * @internal
 */
class MetaParser
{
    /**
     * @param \Art4\JsonApiClient\Meta $meta
     *
     * @return \Swis\JsonApi\Client\Meta
     */
    public function parse(JsonApiMeta $meta): Meta
    {
        return new Meta($meta->asArray(true));
    }
}
