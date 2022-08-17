<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client\Actions;

trait Delete
{
    /**
     * @param string $id
     * @param array  $parameters
     *
     * @return \Swis\JsonApi\Client\Interfaces\DocumentInterface
     */
    public function delete(string $id, array $parameters = [])
    {
        return $this->getClient()->delete($this->getEndpoint().'/'.urlencode($id).'?'.http_build_query($parameters));
    }
}
