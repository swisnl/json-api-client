<?php

namespace Swis\JsonApi\Guzzle;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class FixturesHandler extends MockHandler
{
    const TYPE_BODY = 'mock';

    const TYPE_HEADERS = 'headers';

    /**
     * @var string
     */
    private $fixturesPath;

    /**
     * @var array
     */
    private $domainAliases = [];

    /**
     * FixturesHandler constructor.
     *
     * @param string        $fixturesPath
     * @param array|null    $queue
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function __construct(
        string $fixturesPath,
        array $queue = null,
        callable $onFulfilled = null,
        callable $onRejected = null
    ) {
        $this->fixturesPath = $fixturesPath;
        parent::__construct($queue, $onFulfilled, $onRejected);
    }

    /**
     * @return array
     */
    public function getDomainAliases(): array
    {
        return $this->domainAliases;
    }

    /**
     * @param array $domainAliases
     */
    public function setDomainAliases(array $domainAliases)
    {
        $this->domainAliases = $domainAliases;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $options
     *
     * @return $this|\GuzzleHttp\Promise\Promise|\GuzzleHttp\Promise\PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $file = $this->getFilePathFromRequest($request, self::TYPE_BODY);
        $headers = $this->getHeadersFromRequest($request);
        $this->append(new Response(200, $headers, file_get_contents($file)));

        return parent::__invoke($request, $options);
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
    protected function getFilePathFromRequest(RequestInterface $request, string $type)
    {
        $fixturesPath = rtrim($this->fixturesPath, '/');
        $host = $this->getFixtureHost(trim($request->getUri()->getHost(), '/'));
        $path = trim($request->getUri()->getPath(), '/');

        $pathToFile = implode('/', [$fixturesPath, $host, $path]);
        $fileWithMethod = implode('.', [$pathToFile, strtolower($request->getMethod()), $type]);

        if (file_exists($fileWithMethod)) {
            $file = $fileWithMethod;
        } else {
            $file = implode('.', [$pathToFile, $type]);
        }

        if (!file_exists($file)) {
            throw new MockNotFoundException("File $file does not exist.");
        }

        $realDir = realpath(dirname($file));

        if ($realDir !== dirname($file)) {
            throw new \RuntimeException("Path to $file is out of bounds.");
        }

        return $file;
    }

    /**
     * @param string $host
     *
     * @return string
     */
    protected function getFixtureHost(string $host): string
    {
        if (array_key_exists($host, $this->domainAliases)) {
            return $this->domainAliases[$host];
        }

        return $host;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function getHeadersFromRequest(RequestInterface $request): array
    {
        $headers = [];

        try {
            $file = $this->getFilePathFromRequest($request, self::TYPE_HEADERS);

            $headers = \GuzzleHttp\json_decode(file_get_contents($file), true);
        } catch (MockNotFoundException $e) {
        }

        return $headers;
    }
}
