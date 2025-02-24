<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ResponseParserInterface
{
    public function parse(ResponseInterface $response): DocumentInterface;
}
