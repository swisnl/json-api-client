<?php

namespace Swis\JsonApi\Interfaces;

use Swis\JsonApi\JsonApi\Hydrator;

interface ParserInterface
{
    /**
     * @param string $json
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function deserialize(string $json): DocumentInterface;

    /**
     * @param \Swis\JsonApi\Interfaces\DocumentInterface $json
     *
     * @return string
     */
    public function serialize(DocumentInterface $json): string;

    /**
     * @return \Swis\JsonApi\JsonApi\Hydrator
     */
    public function getHydrator(): Hydrator;
}
