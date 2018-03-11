<?php

namespace Omnipay\Novalnet\Message;

class CapturePurchaseResponse extends AbstractResponse
{

    public function getCode()
    {
        return $this->data['status'];
    }

    public function getMessage()
    {
        return $this->data['status_desc'];
    }
}
