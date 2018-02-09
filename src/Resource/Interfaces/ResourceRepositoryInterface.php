<?php

namespace Swis\JsonApi\Client\Resource\Interfaces;

use Swis\JsonApi\Client\Interfaces\ItemDocumentInterface;

interface ResourceRepositoryInterface
{
    /**
     * @throws \Swis\JsonApi\Client\Exceptions\DocumentTypeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface
     */
    public function all();

    /**
     * @param $id
     *
     * @throws \Swis\JsonApi\Client\Exceptions\DocumentTypeException
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function find($id);

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function save(ItemDocumentInterface $document);

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface $document
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(ItemDocumentInterface $document);

    /**
     * @param $id
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function deleteById($id);
}
