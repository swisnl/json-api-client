<?php

namespace Swis\JsonApi\Fixtures;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface FixtureResponseBuilderInterface
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function build(RequestInterface $request): ResponseInterface;
}
