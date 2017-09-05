<?php

class ResponseFactoryTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_builds_a_response_from_psrresponse()
    {
        $psrResponse = new \GuzzleHttp\Psr7\Response(200, [], 'test response');
        $response = (new \Swis\JsonApi\ResponseFactory())->make($psrResponse);

        $this->assertInstanceOf(\Swis\JsonApi\Response::class, $response);
        $this->assertEquals(true, $response->hasSuccessfulStatusCode());
        $this->assertEquals('test response', $response->getBody());
    }
}
