<?php

namespace Omnipay\Novalnet\Message;

class PurchaseRequestGiropay extends PurchaseRequest
{
    public $endpoint = 'https://payport.novalnet.de/giropay';
}