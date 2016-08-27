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

class RedirectGateway extends AbstractGateway
{
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
     * @return \Omnipay\Novalnet\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        if (!$this->getChosenOnly()) {
            return $this->determineRequest($parameters);
        }

        return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestAll', $parameters);
    }

    /**
     * Complete a purchase.
     *
     * @param array $parameters
     *
     * @return CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Novalnet\Message\CompletePurchaseRequest', $parameters);
    }

    private function determineRequest(array $parameters = array())
    {
        if (self::GIROPAY_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestGiropay', $parameters);
        }
        if (in_array($this->getPaymentMethod(), array(self::ONLINE_TRANSFER_METHOD, self::IDEAL_METHOD))) {
            return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestIdeal', $parameters);
        }
        if (self::PAYPAL_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestPayPal', $parameters);
        }
        if (self::EPS_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestEps', $parameters);
        }
        if (self::CREDITCARD_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestCreditCard', $parameters);
        }

        return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestAll', $parameters);
    }
}
