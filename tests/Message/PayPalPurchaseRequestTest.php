<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Novalnet\Gateway;

class PayPalPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestPayPal($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return Gateway::PAYPAL_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/paypal_payport';
    }
}