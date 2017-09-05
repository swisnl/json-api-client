<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\ItemDocumentInterface;

class ItemDocumentSerializer
{
    /**
     * @param \Swis\JsonApi\Interfaces\ItemDocumentInterface $itemDocument
     *
     * @return string
     */
    public function serialize(ItemDocumentInterface $itemDocument)
    {
        $document = [];

        if (!empty($itemDocument->getLinks())) {
            $document['links'] = $itemDocument->getLinks();
        }

        if (!empty($itemDocument->getData())) {
            $document['data'] = $itemDocument->getData()->toJsonApiArray();
        }

        if (!empty($itemDocument->getIncluded())) {
            $document['included'] = $itemDocument->getIncluded()->toJsonApiArray();
        }

        if (!empty($itemDocument->getMeta())) {
            $document['meta'] = $itemDocument->getMeta();
        }

        if ($itemDocument->hasErrors()) {
            $document['errors'] = $itemDocument->getErrors()->toArray();
        }

        if (!empty($itemDocument->getJsonapi())) {
            $document['jsonapi'] = $itemDocument->getJsonapi();
        }

        return json_encode($document);
    }
}
