<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface RepositoryInterface
{
    /**
     * @return \Swis\JsonApi\Client\Interfaces\CollectionDocumentInterface<TItem>
     */
    public function all();

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<TItem>
     */
    public function find(string $id);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\ItemDocumentInterface<TItem>
     */
    public function save(ItemInterface $item);

    /**
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $id);
}
