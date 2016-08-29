<?php

namespace Omnipay\Novalnet;


use Omnipay\Novalnet\Message\RedirectCompletePurchaseRequest;

class RedirectGateway extends AbstractGateway
{
    const CREDITCARD_METHOD = 6;
    const ONLINE_TRANSFER_METHOD = 33;
    const PAYPAL_METHOD = 34;
    const IDEAL_METHOD = 49;
    const EPS_METHOD = 50;
    const GIROPAY_METHOD = 69;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Novalnet_Redirect';
    }

    /**
     * Start a purchase request.
     *
     * @param array $parameters An array of options
     *
     * @return \Omnipay\Novalnet\Message\RedirectPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\RedirectPurchaseRequest', $parameters);
    }

    /**
     * Complete a purchase.
     *
     * @param array $parameters
     *
     * @return RedirectCompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\RedirectCompletePurchaseRequest', $parameters);
    }

}
