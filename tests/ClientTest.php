<?php

namespace Swis\JsonApi\Client\Tests;

use Http\Client\Exception\HttpException;
use Http\Discovery\MessageFactoryDiscovery;
use Swis\JsonApi\Client\Client;

class ClientTest extends AbstractTest
{
    /**
     * @test
     */
    public function the_base_url_can_be_changed_after_instantiation()
    {
        $httpclient = new \Http\Mock\Client();

        $client = new Client(
            $httpclient,
            'http://www.test.com',
            MessageFactoryDiscovery::find()
        );

        $this->assertEquals('http://www.test.com', $client->getBaseUri());
        $client->setBaseUri('http://www.test-changed.com');
        $this->assertEquals('http://www.test-changed.com', $client->getBaseUri());
    }

    /**
     * @test
     */
    public function it_builds_a_get_request()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );
        $client->get($endpoint);
        $this->assertEquals('GET', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
    }

    /**
     * @test
     */
    public function it_builds_a_delete_request()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );
        $client->delete($endpoint);
        $this->assertEquals('DELETE', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
    }

    /**
     * @test
     */
    public function it_builds_a_patch_request()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );
        $client->patch($endpoint, 'testvar=testvalue');
        $this->assertEquals('PATCH', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals('testvar=testvalue', $httpClient->getLastRequest()->getBody()->getContents());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
    }

    /**
     * @test
     */
    public function it_builds_a_post_request()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );
        $client->post($endpoint, 'testvar=testvalue');
        $this->assertEquals('POST', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals('testvar=testvalue', $httpClient->getLastRequest()->getBody()->getContents());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
    }

    /**
     * @test
     */
    public function it_passes_http_exceptions()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $request = $this->createMock(\Psr\Http\Message\RequestInterface::class);
        $response = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $exception = HttpException::create($request, $response);
        $httpClient->setDefaultException($exception);

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );

        $httpResponse = $client->get($endpoint);

        $this->assertSame($response, $httpResponse);
    }
}
