<?php

namespace Omnipay\Novalnet;

use Omnipay\Novalnet\Message\CompletePurchaseRequest;
use Omnipay\Novalnet\Message\PurchaseRequestAll;
use Omnipay\Novalnet\Message\PurchaseRequestCreditcard;
use Omnipay\Novalnet\Message\PurchaseRequestEps;
use Omnipay\Novalnet\Message\PurchaseRequestGiropay;
use Omnipay\Novalnet\Message\PurchaseRequestIdeal;
use Omnipay\Novalnet\Message\PurchaseRequestPayPal;
use Omnipay\Novalnet\Message\PurchaseRequestSepa;

class XmlGateway extends AbstractGateway
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Novalnet_Xml';
    }

    /**
     * Start a purchase request.
     *
     * @param array $parameters An array of options
     *
     * @return \Omnipay\Novalnet\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestSepa', $parameters);
    }
}
