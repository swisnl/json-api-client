<?php

namespace Swis\JsonApi;

use Swis\JsonApi\Interfaces\ItemDocumentInterface;

class ItemDocument extends Document implements ItemDocumentInterface, \JsonSerializable
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @see  http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $document = [];

        if (!empty($this->getLinks())) {
            $document['links'] = $this->links;
        }

        if (!empty($this->getData())) {
            $document['data'] = $this->data->toJsonApiArray();
        }

        if (!empty($this->getIncluded())) {
            $document['included'] = $this->getIncluded()->toJsonApiArray();
        }

        if (!empty($this->getMeta())) {
            $document['meta'] = $this->meta;
        }

        if ($this->hasErrors()) {
            $document['errors'] = $this->errors->toArray();
        }

        if (!empty($this->getJsonapi())) {
            $document['jsonapi'] = $this->jsonapi;
        }

        return $document;
    }
}
