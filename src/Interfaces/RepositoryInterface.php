<?php

namespace Swis\JsonApi\Client\Interfaces;

interface RepositoryInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface
     */
    public function all();

    /**
     * @param string $id
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function find(string $id);

    /**
     * @param \Swis\JsonApi\Client\Interfaces\ItemInterface $item
     *
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function save(ItemInterface $item);

    /**
     * @param string $id
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $id);
}
