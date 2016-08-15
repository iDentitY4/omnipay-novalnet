<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Novalnet\Gateway;

class IdealPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestIdeal($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return Gateway::IDEAL_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/online_transfer_payport';
    }
}