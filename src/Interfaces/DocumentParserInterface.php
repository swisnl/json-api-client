<?php

namespace Swis\JsonApi\Client\Interfaces;

interface DocumentParserInterface
{
    /**
     * @param string $json
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function parse(string $json): DocumentInterface;
}
