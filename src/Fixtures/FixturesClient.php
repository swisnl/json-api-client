<?php

namespace Swis\JsonApi\Fixtures;

use Http\Message\ResponseFactory;
use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;

class FixturesClient extends Client
{
    protected $fixtureReponseBuilder;

    /**
     * @param FixtureResponseBuilderInterface $fixtureReponseBuilder
     * @param ResponseFactory|null            $responseFactory
     */
    public function __construct(FixtureResponseBuilderInterface $fixtureReponseBuilder, ResponseFactory $responseFactory = null)
    {
        parent::__construct($responseFactory);

        $this->fixtureReponseBuilder = $fixtureReponseBuilder;
    }

    public function sendRequest(RequestInterface $request)
    {
        $this->setDefaultResponse($this->fixtureReponseBuilder->build($request));

        return parent::sendRequest($request);
    }
}
