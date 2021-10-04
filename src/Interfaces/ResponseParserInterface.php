<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ResponseParserInterface
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function parse(ResponseInterface $response): DocumentInterface;
}
