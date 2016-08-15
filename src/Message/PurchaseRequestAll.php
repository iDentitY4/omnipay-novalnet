<?php

namespace Omnipay\Novalnet\Message;

use Guzzle\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class PurchaseRequestAll extends PurchaseRequest
{
    public $endpoint = 'https://payport.novalnet.de/nn/paygate.jsp';

    public function shouldEncode()
    {
        return false;
    }
}
