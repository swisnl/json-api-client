<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Concerns\HasMeta;

class Link
{
    use HasMeta;

    /**
     * @var string
     */
    protected $href;

    public function __construct(string $href, ?Meta $meta = null)
    {
        $this->href = $href;
        $this->meta = $meta;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function toArray()
    {
        $array = [
            'href' => $this->getHref(),
        ];

        if ($this->getMeta() !== null) {
            $array['meta'] = $this->getMeta()->toArray();
        }

        return $array;
    }
}
