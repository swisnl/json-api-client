<?php

namespace Swis\JsonApi\Client\JsonApi;

use Art4\JsonApiClient\Jsonapi as JsonApiJsonapi;
use Art4\JsonApiClient\Meta as JsonApiMeta;
use Swis\JsonApi\Client\Jsonapi;
use Swis\JsonApi\Client\Meta;

/**
 * @internal
 */
class JsonapiParser
{
    /**
     * @var \Swis\JsonApi\Client\JsonApi\MetaParser
     */
    private $metaParser;

    /**
     * @param \Swis\JsonApi\Client\JsonApi\MetaParser $metaParser
     */
    public function __construct(MetaParser $metaParser)
    {
        $this->metaParser = $metaParser;
    }

    /**
     * @param \Art4\JsonApiClient\Jsonapi $jsonApi
     *
     * @return \Swis\JsonApi\Client\Jsonapi
     */
    public function parse(JsonApiJsonapi $jsonApi): Jsonapi
    {
        return new Jsonapi(
            $jsonApi->has('version') ? $jsonApi->get('version') : null,
            $jsonApi->has('meta') ? $this->buildMeta($jsonApi->get('meta')) : null
        );
    }

    /**
     * @param \Art4\JsonApiClient\Meta $meta
     *
     * @return \Swis\JsonApi\Client\Meta
     */
    private function buildMeta(JsonApiMeta $meta): Meta
    {
        return $this->metaParser->parse($meta);
    }
}
