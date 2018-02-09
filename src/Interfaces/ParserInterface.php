<?php

namespace Swis\JsonApi\Client\Interfaces;

use Swis\JsonApi\Client\JsonApi\Hydrator;

interface ParserInterface
{
    /**
     * @param string $json
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function deserialize(string $json): DocumentInterface;

    /**
     * @param \Swis\JsonApi\Client\Interfaces\DocumentInterface $json
     *
     * @return string
     */
    public function serialize(DocumentInterface $json): string;

    /**
     * @return \Swis\JsonApi\Client\JsonApi\Hydrator
     */
    public function getHydrator(): Hydrator;
}
