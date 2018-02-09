<?php

namespace Swis\JsonApi\Client\Tests\Fixtures;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Swis\JsonApi\Client\Fixtures\FixtureResponseBuilderInterface;
use Swis\JsonApi\Client\Fixtures\Guzzle\FixturesHandler;
use Swis\JsonApi\Client\Tests\AbstractTest;

class FixturesHandlerTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_calls_the_fixture_response_builder_on_invoke()
    {
        $request = new Request('GET', new Uri('http://example.com'));
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Swis\JsonApi\Client\Fixtures\FixtureResponseBuilderInterface $responseBuilder */
        $responseBuilder = $this->getMockBuilder(FixtureResponseBuilderInterface::class)->getMock();
        $responseBuilder->expects($this->once())
            ->method('build')
            ->with($request)
            ->willReturn(new Response());

        $handler = new FixturesHandler($responseBuilder);
        /* @noinspection ImplicitMagicMethodCallInspection */
        $handler->__invoke($request, []);
    }
}
