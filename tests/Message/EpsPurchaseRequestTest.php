<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Novalnet\Gateway;

class EpsPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestEps($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return Gateway::EPS_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/eps_payport';
    }
}