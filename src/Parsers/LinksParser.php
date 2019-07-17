<?php

namespace Swis\JsonApi\Client\Parsers;

use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;

/**
 * @internal
 */
class LinksParser
{
    /**
     * @var \Swis\JsonApi\Client\Parsers\MetaParser
     */
    private $metaParser;

    /**
     * @param \Swis\JsonApi\Client\Parsers\MetaParser $metaParser
     */
    public function __construct(MetaParser $metaParser)
    {
        $this->metaParser = $metaParser;
    }

    /**
     * @param mixed $data
     *
     * @return \Swis\JsonApi\Client\Links
     */
    public function parse($data): Links
    {
        return new Links(
            array_map(
                function ($link) {
                    return $this->buildLink($link);
                },
                (array)$data
            )
        );
    }

    /**
     * @param mixed $data
     *
     * @return \Swis\JsonApi\Client\Link
     */
    private function buildLink($data): ? Link
    {
        if ($data === null) {
            return null;
        }

        if (is_string($data)) {
            return new Link($data);
        }

        if (!is_object($data)) {
            throw new ValidationException(sprintf('Link has to be an object, string or null, "%s" given.', gettype($data)));
        }
        if (!property_exists($data, 'href')) {
            throw new ValidationException('Link must have a "href" attribute.');
        }

        return new Link($data->href, property_exists($data, 'meta') ? $this->metaParser->parse($data->meta) : null);
    }
}
