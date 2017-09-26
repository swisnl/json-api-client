<?php

namespace Swis\JsonApi\Tests;

use GuzzleHttp\ClientInterface;
use Swis\JsonApi\Client;
use Swis\JsonApi\RequestFactory;
use Swis\JsonApi\ResponseFactory;

class ClientTest extends AbstractTest
{
    /**
     * @test
     */
    public function the_base_url_can_be_changed_after_instantiation()
    {
        $guzzle = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactory::class);
        $responseFactory = $this->createMock(ResponseFactory::class);

        $client = new Client(
            $guzzle,
            'http://www.test.com',
            $requestFactory,
            $responseFactory
        );

        $this->assertEquals('http://www.test.com', $client->getBaseUri());
        $client->setBaseUri('http://www.test-changed.com');
        $this->assertEquals('http://www.test-changed.com', $client->getBaseUri());
    }

    /**
     * TODO.
     */
    public function it_builds_a_get_request()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $guzzle = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactory::class);
        $responseFactory = $this->createMock(ResponseFactory::class);

        $guzzle->method('send');

        $requestFactory->expects($this->once())->method('make')->with('GET', $baseUri.$endpoint);

        $client = new Client(
            $guzzle,
            $baseUri,
            $requestFactory,
            $responseFactory
        );
        $client->get($endpoint);
    }
}
