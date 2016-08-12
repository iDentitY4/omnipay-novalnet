<?php

namespace Omnipay\Novalnet\Message;

class PurchaseRequestIdeal extends PurchaseRequest
{
    public $endpoint = 'https://payport.novalnet.de/online_transfer_payport';
}