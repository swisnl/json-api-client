<?php

namespace Swis\JsonApi\Interfaces;

interface ResponseInterface
{
    /**
     * @return bool
     */
    public function hasSuccessfulStatusCode(): bool;

    /**
     * @return bool
     */
    public function hasServerErrorStatusCode(): bool;

    /**
     * @return bool
     */
    public function hasBody(): bool;

    /**
     * @return string
     */
    public function getBody(): string;

    /**
     * @param string $header
     *
     * @return bool
     */
    public function hasHeader(string $header): bool;

    /**
     * @param string $header
     *
     * @return string
     */
    public function getHeader(string $header): string;
}
