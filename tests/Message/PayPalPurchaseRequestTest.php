<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\Message\PurchaseRequestPayPal;

class PayPalPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestPayPal($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return AbstractGateway::PAYPAL_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/paypal_payport';
    }
}