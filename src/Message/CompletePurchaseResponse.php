<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;

class CompletePurchaseResponse extends AbstractResponse
{

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed            $data
     *
     * @throws InvalidResponseException
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;

        $originalTransactionId = (string) $this->request->getTransactionId();
        $transactionId = (string) $this->getTransactionId();

        if ($originalTransactionId && $transactionId && $transactionId !== $originalTransactionId) {
            throw new InvalidResponseException(
                'The transactionId in the parameters ('.$originalTransactionId.') '.
                'does not match the transactionId from the gateway: ' . $transactionId
            );
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
        if (isset($this->data->transaction_status) && isset($this->data->transaction_status->status_message)) {
            return (string) $this->data->transaction_status->status_message;
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
            return (int) $this->data->amount;
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
            return (boolean) $this->data->test_mode;
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

    public function getTransactionReference()
    {
        if (isset($this->data->tid)) {
            return (string) $this->data->tid;
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

    public function getPaymentMethod()
    {
        if (isset($this->data->payment_type)) {
            return (string) $this->data->payment_type;
        }

        return null;
    }
}
