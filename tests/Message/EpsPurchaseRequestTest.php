<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\Message\PurchaseRequestEps;

class EpsPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestEps($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return AbstractGateway::EPS_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/eps_payport';
    }
}