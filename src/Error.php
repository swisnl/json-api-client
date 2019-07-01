<?php

namespace Swis\JsonApi\Client;

use Swis\JsonApi\Client\Traits\HasLinks;
use Swis\JsonApi\Client\Traits\HasMeta;

class Error
{
    use HasLinks;
    use HasMeta;

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $status;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $detail;

    /**
     * @var \Swis\JsonApi\Client\ErrorSource|null
     */
    protected $source;

    /**
     * @param string|null                           $id
     * @param \Swis\JsonApi\Client\Links|null       $links
     * @param string|null                           $status
     * @param string|null                           $code
     * @param string|null                           $title
     * @param string|null                           $detail
     * @param \Swis\JsonApi\Client\ErrorSource|null $source
     * @param \Swis\JsonApi\Client\Meta|null        $meta
     */
    public function __construct(
        string $id = null,
        Links $links = null,
        string $status = null,
        string $code = null,
        string $title = null,
        string $detail = null,
        ErrorSource $source = null,
        Meta $meta = null
    ) {
        $this->id = $id;
        $this->links = $links;
        $this->status = $status;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->source = $source;
        $this->meta = $meta;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @return \Swis\JsonApi\Client\ErrorSource|null
     */
    public function getSource()
    {
        return $this->source;
    }
}
