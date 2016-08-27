<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\Message\PurchaseRequestIdeal;

class IdealPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestIdeal($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return AbstractGateway::IDEAL_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/online_transfer_payport';
    }
}