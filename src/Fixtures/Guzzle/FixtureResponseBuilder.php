<?php

namespace Swis\JsonApi\Fixtures\Guzzle;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Fixtures\FixtureResponseBuilder as BaseFixtureResponseBuilder;
use Swis\JsonApi\Fixtures\FixtureResponseBuilderInterface;

class FixtureResponseBuilder extends BaseFixtureResponseBuilder
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \RuntimeException
     * @throws \Swis\JsonApi\Fixtures\MockNotFoundException
     *
     * @return ResponseInterface
     */
    public function build(RequestInterface $request): ResponseInterface
    {
        $response = new Response(
            $this->getMockStatusForRequest($request),
            $this->getMockHeadersForRequest($request),
            $this->getMockBodyForRequest($request)
        );

        return $response;
    }
}
