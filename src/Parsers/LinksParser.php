<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Parsers;

use Swis\JsonApi\Client\Collection;
use Swis\JsonApi\Client\Exceptions\ValidationException;
use Swis\JsonApi\Client\Link;
use Swis\JsonApi\Client\Links;

/**
 * @internal
 */
class LinksParser
{
    public const SOURCE_DOCUMENT = 'document';

    public const SOURCE_ERROR = 'error';

    public const SOURCE_ITEM = 'item';

    public const SOURCE_RELATIONSHIP = 'relationship';

    private const LINKS_THAT_MAY_NOT_BE_NULL_WHEN_PRESENT = [
        'self',
        'related',
    ];

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
     * @param mixed  $data
     * @param string $source
     *
     * @return \Swis\JsonApi\Client\Links
     */
    public function parse($data, string $source): Links
    {
        if (!is_object($data)) {
            throw new ValidationException(sprintf('Links MUST be an object, "%s" given.', gettype($data)));
        }
        if ($source === self::SOURCE_RELATIONSHIP && !property_exists($data, 'self') && !property_exists($data, 'related')) {
            throw new ValidationException('Relationship links object MUST contain at least one of the following properties: `self`, `related`.');
        }

        return new Links(
            Collection::wrap((array) $data)
                ->map(fn ($link, $name) => $this->buildLink($link, $name))
                ->toArray()
        );
    }

    /**
     * @param mixed  $data
     * @param string $name
     *
     * @return \Swis\JsonApi\Client\Link
     */
    private function buildLink($data, string $name): ?Link
    {
        if (in_array($name, self::LINKS_THAT_MAY_NOT_BE_NULL_WHEN_PRESENT, true) && !is_string($data) && !is_object($data)) {
            throw new ValidationException(sprintf('Link "%s" MUST be an object or string, "%s" given.', $name, gettype($data)));
        }

        if ($data === null) {
            return null;
        }

        if (is_string($data)) {
            return new Link($data);
        }

        if (!is_object($data)) {
            throw new ValidationException(sprintf('Link "%s" MUST be an object, string or null, "%s" given.', $name, gettype($data)));
        }
        if (!property_exists($data, 'href')) {
            throw new ValidationException(sprintf('Link "%s" MUST have a "href" attribute.', $name));
        }

        return new Link($data->href, property_exists($data, 'meta') ? $this->metaParser->parse($data->meta) : null);
    }
}
