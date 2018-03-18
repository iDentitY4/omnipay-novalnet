<?php

namespace Omnipay\Novalnet;

class InfoportGateway extends AbstractGateway
{
    const SUBSCRIPTION_STOP = 'SUBSCRIPTION_STOP';
    const SUBSCRIPTION_UPDATE = 'SUBSCRIPTION_UPDATE';
    const SUBSCRIPTION_PAUSE = 'SUBSCRIPTION_PAUSE';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Novalnet_Infoport';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array(
            'vendorId' => null,
            'vendorAuthcode' => null,
            'requestType' => null,
        );
    }

    public function getRequestType()
    {
        return $this->getParameter('requestType');
    }

    public function setRequestType($value)
    {
        return $this->setParameter('requestType', $value);
    }

    /**
     * Start a purchase request.
     *
     * @param array $parameters An array of options
     *
     * @return \Omnipay\Novalnet\Message\InfoportRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\InfoportRequest', $parameters);
    }
}
