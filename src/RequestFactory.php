<?php

namespace Swis\JsonApi;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestFactory
{
    /**
     * @param string                                                                         $method
     * @param string                                                                         $endpoint
     * @param resource|string|null|int|float|bool|\Psr\Http\Message\StreamInterface|callable $body
     * @param array                                                                          $headers
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function make(string $method, string $endpoint, $body = null, array $headers = []): RequestInterface
    {
        $request = new Request($method, $endpoint);

        if ($body) {
            $request = $request->withBody(\GuzzleHttp\Psr7\stream_for($body));
        }

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }
}
