<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseResponse extends AbstractResponse
{
    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        $originalTransactionId = (string) $this->request->getTransactionId();
        $transactionId = (string) $this->getTransactionId();

        if ($originalTransactionId && $transactionId !== $originalTransactionId) {
            throw new InvalidResponseException(
                'The transactionId in the parameters ('.$originalTransactionId.') '.
                'does not match the transactionId from the gateway: ' . $transactionId
            );
        }

        return $this->getStatus() == 100;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        $this->getStatus();
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
            return (int) $this->data->amount;
        }

        return null;
    }

    public function getCustomerNumber()
    {
        if (isset($this->data->customer_no)) {
            return (int) $this->data->customer_no;
        }

        return null;
    }

    public function getTestMode()
    {
        if (isset($this->data->test_mode)) {
            return (boolean) $this->data->test_mode;
        }

        return false;
    }

    public function getCurrency()
    {
        if (isset($this->data->currency)) {
            return (string) $this->data->currency;
        }

        return false;
    }

    public function getTransactionReference()
    {
        if (isset($this->data->tid)) {
            return (int) $this->data->tid;
        }

        return false;
    }

    public function getTransactionId()
    {
        if (isset($this->data->order_no)) {
            return (int) $this->data->order_no;
        }

        return false;
    }

    public function getPaymentMethod()
    {
        if (isset($this->data->payment_type)) {
            return (string) $this->data->payment_type;
        }

        return null;
    }
}
