<?php

namespace Swis\JsonApi\Client\Tests;

use Http\Client\Common\Exception\LoopException;
use Http\Client\Exception\HttpException;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Client;

class ClientTest extends AbstractTest
{
    /**
     * @test
     */
    public function the_base_url_can_be_changed_after_instantiation()
    {
        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
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
    public function the_default_headers_can_be_changed_after_instantiation()
    {
        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
            'http://www.test.com',
            MessageFactoryDiscovery::find()
        );

        $this->assertEquals(
            [
                'Accept'       => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
            $client->getDefaultHeaders()
        );
        $client->setDefaultHeaders(
            [
                'Accept'       => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'X-Foo'        => 'bar',
            ]
        );
        $this->assertEquals(
            [
                'Accept'       => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'X-Foo'        => 'bar',
            ],
            $client->getDefaultHeaders()
        );
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
        $response = $client->get($endpoint, ['X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('GET', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept'       => ['application/vnd.api+json'],
                'Content-Type' => ['application/vnd.api+json'],
                'X-Foo'        => ['bar'],
                'Host'         => ['www.test.com'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
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
        $response = $client->delete($endpoint, ['X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('DELETE', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept'       => ['application/vnd.api+json'],
                'Content-Type' => ['application/vnd.api+json'],
                'X-Foo'        => ['bar'],
                'Host'         => ['www.test.com'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
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
        $response = $client->patch($endpoint, 'testvar=testvalue', ['Content-Type' => 'application/pdf', 'X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('PATCH', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals('testvar=testvalue', $httpClient->getLastRequest()->getBody()->getContents());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept'       => ['application/vnd.api+json'],
                'Content-Type' => ['application/pdf'],
                'X-Foo'        => ['bar'],
                'Host'         => ['www.test.com'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
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
        $response = $client->post($endpoint, 'testvar=testvalue', ['Content-Type' => 'application/pdf', 'X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('POST', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals('testvar=testvalue', $httpClient->getLastRequest()->getBody()->getContents());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept'       => ['application/vnd.api+json'],
                'Content-Type' => ['application/pdf'],
                'X-Foo'        => ['bar'],
                'Host'         => ['www.test.com'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
    }

    /**
     * @test
     */
    public function it_builds_other_requests()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );
        $response = $client->request('OPTIONS', $endpoint, null, ['Content-Type' => 'application/pdf', 'X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('OPTIONS', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept'       => ['application/vnd.api+json'],
                'Content-Type' => ['application/pdf'],
                'X-Foo'        => ['bar'],
                'Host'         => ['www.test.com'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
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

    /**
     * @test
     */
    public function it_throws_non_http_exceptions()
    {
        $baseUri = 'http://www.test.com';
        $endpoint = '/test/1';

        $httpClient = new \Http\Mock\Client();

        $request = $this->createMock(\Psr\Http\Message\RequestInterface::class);

        $exception = new LoopException('Whoops, detected a loop!', $request);
        $httpClient->setDefaultException($exception);

        $client = new Client(
            $httpClient,
            $baseUri,
            MessageFactoryDiscovery::find()
        );

        $this->expectException(LoopException::class);

        $client->get($endpoint);
    }
}
