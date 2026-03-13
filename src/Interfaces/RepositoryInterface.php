<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Interfaces;

/**
 * @template TItem of \Swis\JsonApi\Client\Interfaces\ItemInterface
 */
interface RepositoryInterface
{
    /**
     * @return CollectionDocumentInterface<TItem>
     */
    public function all();

    /**
     * @return ItemDocumentInterface<TItem>
     */
    public function find(string $id);

    /**
     * @return ItemDocumentInterface<TItem>
     */
    public function save(ItemInterface $item);

    /**
     * @return DocumentInterface
     */
    public function delete(string $id);
}
