<?php

namespace Omnipay\Novalnet\Message;

class PurchaseResponseSepa extends AbstractResponse
{
    public function getAmount()
    {
        return isset($this->data->amount) ? (int) $this->data->amount : null;
    }
}
