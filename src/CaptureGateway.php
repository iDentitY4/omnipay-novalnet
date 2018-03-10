<?php

namespace Omnipay\Novalnet;

class CaptureGateway extends AbstractGateway
{
    const CREDITCARD_METHOD = 6;
    const DIRECT_DEBIT_SEPA_METHOD = 37;
    const PAYPAL_METHOD = 34;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Novalnet_Capture';
    }

    /**
     * Start a purchase request.
     *
     * @param array $parameters An array of options
     *
     * @return \Omnipay\Novalnet\Message\XmlPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\CapturePurchaseRequest', $parameters);
    }
}
