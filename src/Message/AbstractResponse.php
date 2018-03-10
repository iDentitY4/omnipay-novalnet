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
        return isset($this->data->order_no) ? $this->data->order_no : null;
    }

    public function getTransactionReference()
    {
        return isset($this->data->tid) ? $this->data->tid : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return (string) $this->data->status_desc;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return isset($this->data->status) ? (int) $this->data->status : null;
    }

    public function getAmount()
    {
        return isset($this->data->amount) ? (int) $this->data->amount : null;
    }

    public function getCurrency()
    {
        return isset($this->data->currency) ? (string) $this->data->currency : null;
    }

    public function getPaidUntil()
    {
        return isset($this->data->paid_until) ? $this->data->paid_until : null;
    }

    public function getInternalStatusDetails()
    {
        return isset($this->data->internal_status_details) ? $this->data->internal_status_details : null;
    }

    public function getOnHold()
    {
        return isset($this->data->on_hold) ? $this->data->on_hold : null;
    }
}
