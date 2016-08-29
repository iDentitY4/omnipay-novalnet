<?php

namespace Omnipay\Novalnet;

class XmlGateway extends AbstractGateway
{
    const CREDITCARD_METHOD = 'CREDITCARD';
    const DIRECT_DEBIT_SEPA_METHOD = 'DIRECT_DEBIT_SEPA';

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
     * @return \Omnipay\Novalnet\Message\XmlPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\XmlPurchaseRequest', $parameters);
    }
}
