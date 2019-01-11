<?php

namespace Swis\JsonApi\Client\Relations;

use Swis\JsonApi\Client\Interfaces\DataInterface;

class MorphToRelation extends AbstractOneRelation
{
    /**
     * {@inheritdoc}
     */
    public function associate(DataInterface $included)
    {
        parent::associate($included);

        /* @var \Swis\JsonApi\Client\Interfaces\ItemInterface $included */

        $this->type = $included->getType();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dissociate()
    {
        parent::dissociate();

        $this->type = null;

        return $this;
    }
}
