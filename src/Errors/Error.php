<?php

namespace Swis\JsonApi\Client\Errors;

class Error
{
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
     * @var \Swis\JsonApi\Client\Errors\ErrorSource|null
     */
    protected $source;

    /**
     * @var \Swis\JsonApi\Client\Errors\ErrorMeta|null
     */
    protected $meta;

    /**
     * @param string|null                                  $id
     * @param string|null                                  $status
     * @param string|null                                  $code
     * @param string|null                                  $title
     * @param string|null                                  $detail
     * @param \Swis\JsonApi\Client\Errors\ErrorSource|null $source
     * @param \Swis\JsonApi\Client\Errors\ErrorMeta|null   $meta
     */
    public function __construct(
        string $id = null,
        string $status = null,
        string $code = null,
        string $title = null,
        string $detail = null,
        ErrorSource $source = null,
        ErrorMeta $meta = null
    ) {
        $this->id = $id;
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
     * @return \Swis\JsonApi\Client\Errors\ErrorSource|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return \Swis\JsonApi\Client\Errors\ErrorMeta|null
     */
    public function getMeta()
    {
        return $this->meta;
    }
}
