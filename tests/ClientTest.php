<?php

class ClientTest extends AbstractTest
{
    /**
     * @test
     */
    public function the_base_url_can_be_changed_after_instantiation()
    {
        $guzzle = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $requestFactory = $this->createMock(\Swis\JsonApi\RequestFactory::class);
        $responseFactory = $this->createMock(\Swis\JsonApi\ResponseFactory::class);

        $client = new \Swis\JsonApi\Client(
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

        $guzzle = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $requestFactory = $this->createMock(\Swis\JsonApi\RequestFactory::class);
        $responseFactory = $this->createMock(\Swis\JsonApi\ResponseFactory::class);

        $guzzle->method('send');

        $requestFactory->expects($this->once())->method('make')->with('GET', $baseUri.$endpoint);

        $client = new \Swis\JsonApi\Client(
            $guzzle,
            $baseUri,
            $requestFactory,
            $responseFactory
        );
        $client->get($endpoint);
    }
}
