<?php

namespace Swis\JsonApi;

use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     *
     * @return \Swis\JsonApi\Response
     */
    public function make(ResponseInterface $psrResponse): Response
    {
        return new Response($psrResponse);
    }
}
