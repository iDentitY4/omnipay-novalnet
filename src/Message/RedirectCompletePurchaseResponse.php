<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Novalnet\Helpers\RedirectEncode;

class RedirectCompletePurchaseResponse extends AbstractResponse
{

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        // For encoded parameters, check the hash
        if ($this->isSuccessful() && $request->shouldVerifyHash()) {
            $validHash = RedirectEncode::checkHash((array) $data, $request->getPaymentKey());
            if (! $validHash) {
                $this->data->status_text = 'Invalid hash';
                $this->data->status = -1;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return $this->getStatus() === 100;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        if (isset($this->data->status_text)) {
            return (string) $this->data->status_text;
        }

        if (isset($this->data->status_desc)) {
            return (string) $this->data->status_desc;
        }

        return null;
    }

    public function getStatus()
    {
        if (isset($this->data->status)) {
            return (int) $this->data->status;
        }

        return null;
    }

    public function getAmount()
    {
        if (isset($this->data->amount)) {
            return (int) RedirectEncode::decode($this->data->amount);
        }

        return null;
    }

    public function getCustomerNumber()
    {
        if (isset($this->data->customer_no)) {
            return (string) $this->data->customer_no;
        }

        return null;
    }

    public function getTestMode()
    {
        if (isset($this->data->test_mode)) {
            return (boolean) RedirectEncode::decode($this->data->test_mode);
        }

        return false;
    }

    public function getCurrency()
    {
        if (isset($this->data->currency)) {
            return (string) $this->data->currency;
        }

        return null;
    }

    public function getTransactionId()
    {
        if (isset($this->data->order_no)) {
            return (string) $this->data->order_no;
        }

        return null;
    }

    public function getTransactionReference()
    {
        if (isset($this->data->tid)) {
            return (string) $this->data->tid;
        }

        return null;
    }

    public function getPaymentMethod()
    {
        if (isset($this->data->payment_type)) {
            return (string) $this->data->payment_type;
        }

        return null;
    }
}
