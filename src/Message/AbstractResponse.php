<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Message\AbstractResponse as BaseAbstractResponse;
use Omnipay\Common\Message\RequestInterface;

abstract class AbstractResponse extends BaseAbstractResponse
{

    /**
     * @var string
     */
    protected $code;

    /**
     * {@inheritdoc}
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        if (isset($this->data->error)) {
            $this->code = (string) $this->data->error->errorcode;
            $this->data = (string) $this->data->error->errormessage;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->getCode() == 100;
    }

    /**
     * {@inheritdoc}
     */
    public function inTestMode()
    {
        return isset($this->data->test_mode) ? $this->data->test_mode == 1 : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionId()
    {
        return isset($this->data->tid) ? $this->data->tid : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return (string) $this->data->status_message;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return (int) isset($this->data->status) ? $this->data->status : null;
    }
}
