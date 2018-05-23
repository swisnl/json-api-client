<?php

namespace Swis\JsonApi\Client\Exceptions;

use Swis\JsonApi\Interfaces\DocumentInterface;

class DocumentTypeException extends \Exception
{
    /**
     * @var \Swis\JsonApi\Interfaces\DocumentInterface
     */
    private $document;

    /**
     * @param string                                     $message
     * @param \Swis\JsonApi\Interfaces\DocumentInterface $document
     * @param int                                        $code
     * @param \Throwable|null                            $previous
     */
    public function __construct(string $message = '', DocumentInterface $document, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->document = $document;
    }

    /**
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function getDocument(): DocumentInterface
    {
        return $this->document;
    }
}
