<?php

namespace Swis\JsonApi\Tests\Fixtures;

use Swis\JsonApi\Exceptions\DocumentTypeException;
use Swis\JsonApi\ItemDocument;
use Swis\JsonApi\Tests\AbstractTest;

class DocumentTypeExceptionTest extends AbstractTest
{
    /**
     * @test
     */
    public function it_can_get_document_from_exception()
    {
        $document = new ItemDocument();
        $exception = new DocumentTypeException('message', $document);

        $this->assertSame($document, $exception->getDocument());
    }
}
