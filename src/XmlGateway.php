<?php

namespace Omnipay\Novalnet;

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
     * @return \Omnipay\Novalnet\Message\XmlPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\XmlPurchaseRequest', $parameters);
    }
}
