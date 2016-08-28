<?php

namespace Omnipay\Novalnet\Message;

class XmlPurchaseResponse extends AbstractResponse
{
    public function getAmount()
    {
        return isset($this->data->amount) ? (int) $this->data->amount : null;
    }
}
