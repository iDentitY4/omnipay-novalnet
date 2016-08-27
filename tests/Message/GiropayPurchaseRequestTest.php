<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\Message\PurchaseRequestGiropay;

class GiropayPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestGiropay($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return AbstractGateway::GIROPAY_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/giropay';
    }
}