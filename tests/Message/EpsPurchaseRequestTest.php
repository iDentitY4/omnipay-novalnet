<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\Gateway;
use Omnipay\Novalnet\Message\PurchaseRequestEps;

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