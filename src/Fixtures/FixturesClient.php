<?php

namespace Swis\JsonApi\Fixtures;

use Http\Message\ResponseFactory;
use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;

class FixturesClient extends Client
{
    protected $fixtureResponseBuilder;

    /**
     * @param FixtureResponseBuilderInterface $fixtureReponseBuilder
     * @param ResponseFactory|null            $responseFactory
     */
    public function __construct(FixtureResponseBuilderInterface $fixtureReponseBuilder, ResponseFactory $responseFactory = null)
    {
        parent::__construct($responseFactory);

        $this->fixtureResponseBuilder = $fixtureReponseBuilder;
    }

    public function sendRequest(RequestInterface $request)
    {
        $this->setDefaultResponse($this->fixtureResponseBuilder->build($request));

        return parent::sendRequest($request);
    }
}
