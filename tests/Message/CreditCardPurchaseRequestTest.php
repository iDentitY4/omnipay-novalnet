<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Novalnet\Gateway;

class CreditCardPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestCreditCard($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return Gateway::CREDITCARD_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/global_pci_payport';
    }
}