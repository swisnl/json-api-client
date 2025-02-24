<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

interface RepositoryInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface
     */
    public function all();

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function find(string $id);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface
     */
    public function save(ItemInterface $item);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $id);
}
