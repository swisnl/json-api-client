<?php

namespace Swis\JsonApi\Guzzle;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;

class FixtureResponseBuilder implements FixtureResponseBuilderInterface
{
    /**
     * @var string
     */
    const TYPE_BODY = 'mock';

    /**
     * @var string
     */
    const TYPE_HEADERS = 'headers';

    /**
     * @var string
     */
    const TYPE_STATUS = 'status';

    /**
     * @var string
     */
    private $fixturesPath;

    /**
     * @var array
     */
    private $domainAliases;

    /**
     * @param string $fixturesPath
     * @param array  $domainAliases
     */
    public function __construct(string $fixturesPath, array $domainAliases = [])
    {
        $this->fixturesPath = $fixturesPath;
        $this->domainAliases = $domainAliases;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \RuntimeException
     * @throws \Swis\JsonApi\Guzzle\MockNotFoundException
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function build(RequestInterface $request): Response
    {
        return new Response(
            $this->getMockStatusForRequest($request),
            $this->getMockHeadersForRequest($request),
            $this->getMockBodyForRequest($request)
        );
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \RuntimeException
     *
     * @return int
     */
    protected function getMockStatusForRequest(RequestInterface $request): int
    {
        $status = 200;

        try {
            $file = $this->getMockFilePathForRequest($request, self::TYPE_STATUS);

            $status = (int)file_get_contents($file);
        } catch (MockNotFoundException $e) {
        }

        return $status;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function getMockHeadersForRequest(RequestInterface $request): array
    {
        $headers = [];

        try {
            $file = $this->getMockFilePathForRequest($request, self::TYPE_HEADERS);

            $headers = \GuzzleHttp\json_decode(file_get_contents($file), true);
        } catch (MockNotFoundException $e) {
        }

        return $headers;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \RuntimeException
     * @throws \Swis\JsonApi\Guzzle\MockNotFoundException
     *
     * @return string
     */
    protected function getMockBodyForRequest(RequestInterface $request): string
    {
        $file = $this->getMockFilePathForRequest($request, self::TYPE_BODY);

        return file_get_contents($file);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string                             $type
     *
     * @throws \Swis\JsonApi\Guzzle\MockNotFoundException
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function getMockFilePathForRequest(RequestInterface $request, string $type): string
    {
        $possiblePaths = $this->getPossibleMockFilePathsForRequest($request, $type);

        $file = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $file = $path;
                break;
            }
        }

        if (null === $file) {
            throw new MockNotFoundException(
                'No fixture file found. Check possiblePaths for files that can be used.',
                $possiblePaths
            );
        }

        if (realpath(\dirname($file)) !== \dirname($file)) {
            throw new \RuntimeException("Path to $file is out of bounds.");
        }

        return $file;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string                             $type
     *
     * @return array
     */
    protected function getPossibleMockFilePathsForRequest(RequestInterface $request, string $type): array
    {
        $fixturesPath = $this->getFixturesPath();
        $host = $this->getHostFromRequest($request);
        $path = $this->getPathFromRequest($request);
        $method = $this->getMethodFromRequest($request);
        $query = $this->getQueryFromRequest($request);

        $basePathToFile = implode('/', [$fixturesPath, $host, $path]);

        $possibleFiles = [];

        if ('' !== $query) {
            $possibleFiles[] = implode('.', [$basePathToFile, $query, $method, $type]);
            $possibleFiles[] = implode('.', [$basePathToFile, $query, $type]);
        }

        $possibleFiles[] = implode('.', [$basePathToFile, $method, $type]);
        $possibleFiles[] = implode('.', [$basePathToFile, $type]);

        return $possibleFiles;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return string
     */
    protected function getHostFromRequest(RequestInterface $request): string
    {
        $host = trim($request->getUri()->getHost(), '/');

        if (array_key_exists($host, $this->domainAliases)) {
            return $this->domainAliases[$host];
        }

        return $host;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return string
     */
    protected function getPathFromRequest(RequestInterface $request): string
    {
        return trim($request->getUri()->getPath(), '/');
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return string
     */
    protected function getMethodFromRequest(RequestInterface $request): string
    {
        return strtolower($request->getMethod());
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param string                             $separator
     *
     * @return string
     */
    protected function getQueryFromRequest(RequestInterface $request, $separator = '-'): string
    {
        $query = urldecode($request->getUri()->getQuery());
        $parts = array_map(
            function (string $part) use ($separator) {
                return str_replace('=', $separator, $part);
            },
            explode('&', $query)
        );
        sort($parts);

        return Str::slug(implode($separator, $parts), $separator);
    }

    /**
     * @return string
     */
    protected function getFixturesPath(): string
    {
        return rtrim($this->fixturesPath, '/');
    }
}
