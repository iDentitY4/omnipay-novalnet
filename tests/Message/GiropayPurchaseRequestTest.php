<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Novalnet\Gateway;

class GiropayPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestGiropay($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return Gateway::GIROPAY_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/giropay';
    }
}