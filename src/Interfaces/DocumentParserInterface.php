<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface DocumentParserInterface
{
    public function parse(string $json): DocumentInterface;
}
