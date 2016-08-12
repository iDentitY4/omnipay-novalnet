<?php

namespace Omnipay\Novalnet\Message;

class PurchaseRequestPayPal extends PurchaseRequest
{
    public $endpoint = 'https://payport.novalnet.de/paypal_payport';
}