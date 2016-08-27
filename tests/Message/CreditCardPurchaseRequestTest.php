<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\Message\PurchaseRequestCreditCard;

class CreditCardPurchaseRequestTest extends AbstractPurchaseRequestTest
{
    protected function getRequest()
    {
        return new PurchaseRequestCreditCard($this->getHttpClient(), $this->getHttpRequest());
    }

    protected function getPaymentMethod()
    {
        return AbstractGateway::CREDITCARD_METHOD;
    }

    protected function getRedirectUrl()
    {
        return 'https://payport.novalnet.de/global_pci_payport';
    }
}