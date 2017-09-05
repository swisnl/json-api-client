<?php

namespace Swis\JsonApi\Resource\Interfaces;

use Swis\JsonApi\Interfaces\ItemDocumentInterface;

interface ResourceRepositoryInterface
{
    /**
     * @throws \Swis\JsonApi\Exceptions\DocumentTypeException
     *
     * @return \Swis\JsonApi\Interfaces\CollectionDocumentInterface
     */
    public function all();

    /**
     * @param $id
     *
     * @throws \Swis\JsonApi\Exceptions\DocumentTypeException
     *
     * @return \Swis\JsonApi\Interfaces\ItemDocumentInterface
     */
    public function find($id);

    /**
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $document
     *
     * @return \Swis\JsonApi\Interfaces\ItemDocumentInterface
     */
    public function save(ItemDocumentInterface $document);

    /**
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $document
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function delete(ItemDocumentInterface $document);

    /**
     * @param $id
     *
     * @return \Swis\JsonApi\Interfaces\DocumentInterface
     */
    public function deleteById($id);
}
