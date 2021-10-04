<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Tests;

use GuzzleHttp\Psr7\Utils;
use Http\Mock\Client as HttpMockClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Swis\JsonApi\Client\Client;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function itCanGetAndSetTheBaseUrl()
    {
        $client = new Client();

        $this->assertEquals('', $client->getBaseUri());
        $client->setBaseUri('http://www.test-changed.com');
        $this->assertEquals('http://www.test-changed.com', $client->getBaseUri());
    }

    /**
     * @test
     */
    public function itCanGetAndSetTheDefaultHeaders()
    {
        $client = new Client();

        $this->assertEquals(
            [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
            $client->getDefaultHeaders()
        );
        $client->setDefaultHeaders(
            [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'X-Foo' => 'bar',
            ]
        );
        $this->assertEquals(
            [
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
                'X-Foo' => 'bar',
            ],
            $client->getDefaultHeaders()
        );
    }

    /**
     * @test
     */
    public function itBuildsAGetRequest()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $endpoint = '/test/1';

        $response = $client->get($endpoint, ['X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('GET', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept' => ['application/vnd.api+json'],
                'Content-Type' => ['application/vnd.api+json'],
                'X-Foo' => ['bar'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
    }

    /**
     * @test
     */
    public function itBuildsADeleteRequest()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $endpoint = '/test/1';

        $response = $client->delete($endpoint, ['X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('DELETE', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept' => ['application/vnd.api+json'],
                'Content-Type' => ['application/vnd.api+json'],
                'X-Foo' => ['bar'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
    }

    /**
     * @test
     */
    public function itBuildsAPatchRequest()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $endpoint = '/test/1';

        $response = $client->patch($endpoint, 'testvar=testvalue', ['Content-Type' => 'application/pdf', 'X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('PATCH', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals('testvar=testvalue', (string) $httpClient->getLastRequest()->getBody());
        $this->assertEquals($endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept' => ['application/vnd.api+json'],
                'Content-Type' => ['application/pdf'],
                'X-Foo' => ['bar'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
    }

    /**
     * @test
     */
    public function itBuildsAPostRequest()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $endpoint = '/test/1';

        $response = $client->post($endpoint, 'testvar=testvalue', ['Content-Type' => 'application/pdf', 'X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('POST', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals('testvar=testvalue', (string) $httpClient->getLastRequest()->getBody());
        $this->assertEquals($endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept' => ['application/vnd.api+json'],
                'Content-Type' => ['application/pdf'],
                'X-Foo' => ['bar'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
    }

    /**
     * @test
     */
    public function itBuildsOtherRequests()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $endpoint = '/test/1';

        $response = $client->request('OPTIONS', $endpoint, null, ['Content-Type' => 'application/pdf', 'X-Foo' => 'bar']);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('OPTIONS', $httpClient->getLastRequest()->getMethod());
        $this->assertEquals($endpoint, $httpClient->getLastRequest()->getUri());
        $this->assertEquals(
            [
                'Accept' => ['application/vnd.api+json'],
                'Content-Type' => ['application/pdf'],
                'X-Foo' => ['bar'],
            ],
            $httpClient->getLastRequest()->getHeaders()
        );
    }

    /**
     * @test
     */
    public function itBuildsRequestsWithAStringAsBody()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $body = 'testvar=testvalue';

        $response = $client->request('POST', '/test/1', $body);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('testvar=testvalue', (string) $httpClient->getLastRequest()->getBody());
    }

    /**
     * @test
     */
    public function itBuildsRequestsWithAResourceAsBody()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $body = fopen('php://temp', 'r+');
        fwrite($body, 'testvar=testvalue');

        $response = $client->request('POST', '/test/1', $body);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('testvar=testvalue', (string) $httpClient->getLastRequest()->getBody());
    }

    /**
     * @test
     */
    public function itBuildsRequestsWithAStreamAsBody()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $body = Utils::streamFor('testvar=testvalue');

        $response = $client->request('POST', '/test/1', $body);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('testvar=testvalue', (string) $httpClient->getLastRequest()->getBody());
    }

    /**
     * @test
     */
    public function itBuildsRequestsWithoutABody()
    {
        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);

        $body = null;

        $response = $client->request('POST', '/test/1', $body);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('', (string) $httpClient->getLastRequest()->getBody());
    }

    /**
     * @test
     */
    public function itPrependsTheBaseUriIfTheEndpointIsRelative()
    {
        $baseUri = 'http://example.com/api';
        $endpoint = '/test/1';

        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);
        $client->setBaseUri($baseUri);

        $client->get($endpoint);

        $this->assertEquals($baseUri.$endpoint, $httpClient->getLastRequest()->getUri());
    }

    /**
     * @test
     */
    public function itDoesNotPrependTheBaseUriIfTheEndpointIsAlreadyAbsolute()
    {
        $baseUri = 'http://example.com/api';
        $endpoint = 'http://foo.bar/test/1';

        $httpClient = new HttpMockClient();
        $client = new Client($httpClient);
        $client->setBaseUri($baseUri);

        $client->get($endpoint);

        $this->assertEquals($endpoint, $httpClient->getLastRequest()->getUri());
    }
}
