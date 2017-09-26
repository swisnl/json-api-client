<?php

namespace Swis\JsonApi\Tests;

use Psr\Http\Message\RequestInterface;
use Swis\JsonApi\RequestFactory;

class RequestFactoryTest extends AbstractTest
{
    /**
     * @test
     * @dataProvider methodProvider
     *
     * @param string $method
     */
    public function it_builds_a_request_without_body($method)
    {
        $request = (new RequestFactory())->make($method, 'http://www.test.com');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals($method, $request->getMethod());
        $this->assertEmpty($request->getBody()->getContents());
    }

    /**
     * @test
     * @dataProvider methodProvider
     *
     * @param string $method
     */
    public function it_builds_a_request_with_body($method)
    {
        $request = (new RequestFactory())->make($method, 'http://www.test.com', 'test body content');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals('test body content', $request->getBody()->getContents());
    }

    /**
     * @return array
     */
    public function methodProvider()
    {
        return [
            ['GET'],
            ['POST'],
            ['PATCH'],
            ['DELETE'],
        ];
    }
}
