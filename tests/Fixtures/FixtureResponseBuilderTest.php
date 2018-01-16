<?php

namespace Swis\JsonApi\Tests\Fixtures;

use Http\Discovery\MessageFactoryDiscovery;
use Swis\JsonApi\Fixtures\FixtureResponseBuilder;
use Swis\JsonApi\Fixtures\FixtureResponseBuilderInterface;
use Swis\JsonApi\Fixtures\MockNotFoundException;
use Swis\JsonApi\Tests\AbstractTest;
use function GuzzleHttp\Psr7\stream_for;

class FixtureResponseBuilderTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider getResponses
     *
     * @param string $url
     * @param string $method
     * @param string $expectedMock
     */
    public function it_can_build_a_response(string $url, string $method, string $expectedMock)
    {
        $builder = $this->getBuilder();

        $messageFactory = MessageFactoryDiscovery::find();

        $expectedResponse = $messageFactory->createResponse(
            200,
            null,
            [],
            stream_for(file_get_contents($this->getFixturesPath().'/'.$expectedMock))
        );
        $actualResponse = $builder->build(
            $messageFactory->createRequest($method, $url)
        );

        $this->assertEquals($expectedResponse->getBody()->__toString(), $actualResponse->getBody()->__toString());
    }

    public function getResponses()
    {
        return [
            // Simple
            ['http://example.com/api/articles', 'GET', 'example.com/api/articles.mock'],
            // Nested
            ['http://example.com/api/articles/1', 'GET', 'example.com/api/articles/1.mock'],
            // With query
            ['http://example.com/api/comments?query=json', 'GET', 'example.com/api/comments.query-json.mock'],
            // With query fallback
            ['http://example.com/api/comments?foo=bar', 'GET', 'example.com/api/comments.mock'],
            // With method
            ['http://example.com/api/people', 'GET', 'example.com/api/people.get.mock'],
            // With method fallback
            ['http://example.com/api/people', 'POST', 'example.com/api/people.mock'],
            // With query and method
            ['http://example.com/api/tags?query=json', 'POST', 'example.com/api/tags.query-json.post.mock'],
            // With query and method fallback
            ['http://example.com/api/tags?foo=bar', 'GET', 'example.com/api/tags.mock'],
        ];
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_it_cant_find_a_fixture()
    {
        $this->expectException(MockNotFoundException::class);

        $messageFactory = MessageFactoryDiscovery::find();
        $this->getBuilder()->build($messageFactory->createRequest('GET', 'http://example.com/api/lorem-ipsum'));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_path_is_out_of_bounds()
    {
        $this->expectException(\RuntimeException::class);

        $messageFactory = MessageFactoryDiscovery::find();
        $this->getBuilder()->build($messageFactory->createRequest('GET', 'http://example.com/../../out-of-bounds'));
    }

    /**
     * @test
     */
    public function it_can_build_a_response_using_domain_aliases()
    {
        $messageFactory = MessageFactoryDiscovery::find();

        $expectedResponse = $messageFactory->createResponse(
            200,
            '',
            [],
            stream_for(file_get_contents($this->getFixturesPath().'/example.com/api/articles.mock'))
        );

        $actualResponse = $this->getBuilder()->build($messageFactory->createRequest('GET', 'http://foo.bar/api/articles'));

        $this->assertEquals($expectedResponse->getBody()->__toString(), $actualResponse->getBody()->__toString());
    }

    /**
     * @test
     */
    public function it_can_build_a_response_with_custom_headers()
    {
        $messageFactory = MessageFactoryDiscovery::find();

        $expectedResponse = $messageFactory->createResponse(
            200,
            '',
            ['X-Made-With' => 'PHPUnit'],
            stream_for(file_get_contents($this->getFixturesPath().'/example.com/api/articles.mock'))
        );

        $actualResponse = $this->getBuilder()->build($messageFactory->createRequest('GET', 'http://example.com/api/articles'));

        $this->assertEquals($expectedResponse->getHeaders(), $actualResponse->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_build_a_response_with_custom_status()
    {
        $builder = $this->getBuilder();
        $messageFactory = MessageFactoryDiscovery::find();

        $expectedResponse = $messageFactory->createResponse(
            500,
            '',
            [],
            stream_for(file_get_contents($this->getFixturesPath().'/example.com/api/articles.mock'))
        );
        $actualResponse = $this->getBuilder()->build($messageFactory->createRequest('GET', 'http://example.com/api/articles'));

        $this->assertEquals($expectedResponse->getStatusCode(), $actualResponse->getStatusCode());
    }

    /**
     * @return \Swis\JsonApi\Fixtures\Guzzle\FixtureResponseBuilder
     */
    protected function getBuilder(): FixtureResponseBuilderInterface
    {
        return new FixtureResponseBuilder($this->getFixturesPath(), ['foo.bar' => 'example.com']);
    }

    /**
     * @return string
     */
    protected function getFixturesPath(): string
    {
        return \dirname(__DIR__).'/_fixtures';
    }
}
