<?php

namespace Swis\JsonApi\Client\Fixtures;

use Http\Message\ResponseFactory;
use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;

class FixturesClient extends Client
{
    protected $fixtureResponseBuilder;

    /**
     * @param FixtureResponseBuilderInterface $fixtureResponseBuilder
     * @param ResponseFactory|null            $responseFactory
     */
    public function __construct(FixtureResponseBuilderInterface $fixtureResponseBuilder, ResponseFactory $responseFactory = null)
    {
        parent::__construct($responseFactory);

        $this->fixtureResponseBuilder = $fixtureResponseBuilder;
    }

    public function sendRequest(RequestInterface $request)
    {
        $this->setDefaultResponse($this->fixtureResponseBuilder->build($request));

        return parent::sendRequest($request);
    }
}
