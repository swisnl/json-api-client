<?php

namespace Swis\JsonApi\Tests;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Swis\JsonApi\Response;
use Swis\JsonApi\ResponseFactory;

class ResponseFactoryTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_builds_a_response_from_psrresponse()
    {
        $psrResponse = new GuzzleResponse(200, [], 'test response');
        $response = (new ResponseFactory())->make($psrResponse);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(true, $response->hasSuccessfulStatusCode());
        $this->assertEquals('test response', $response->getBody());
    }
}
