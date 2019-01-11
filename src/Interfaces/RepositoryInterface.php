<?php

namespace Swis\JsonApi\Client\Interfaces;

interface RepositoryInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface
     */
    public function all();

    /**
     * @param $id
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
