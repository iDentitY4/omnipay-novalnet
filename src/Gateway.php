<?php

namespace Omnipay\Novalnet;

use Omnipay\Common\AbstractGateway;

use Omnipay\Novalnet\Message\CompletePurchaseRequest;
use Omnipay\Novalnet\Message\PurchaseRequestAll;
use Omnipay\Novalnet\Message\PurchaseRequestCreditcard;
use Omnipay\Novalnet\Message\PurchaseRequestEps;
use Omnipay\Novalnet\Message\PurchaseRequestGiropay;
use Omnipay\Novalnet\Message\PurchaseRequestIdeal;
use Omnipay\Novalnet\Message\PurchaseRequestPayPal;
use Omnipay\Novalnet\Message\PurchaseRequestSepa;

class Gateway extends AbstractGateway
{
    const EPS_METHOD = 50;
    const CREDITCARD_METHOD = 6;
    const GIROPAY_METHOD = 69;
    const IDEAL_METHOD = 49;
    const ONLINE_TRANSFER_METHOD = 33;
    const PAYPAL_METHOD = 34;
    const SEPA_METHOD = 0;
    const ALL_METHODS = 99;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Novalnet';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array(
            'vendorId' => 4,
            'vendorAuthcode' => 'JyEtHUjjbHNJwVztW6JrafIMHQvici',
            'productId' => 14,
            'tariffId' => 30,
            'testMode' => true,
            'paymentMethod' => self::ALL_METHODS,
        );
    }

    public function getVendorAuthcode()
    {
        return $this->getParameter('vendorAuthcode');
    }

    public function setVendorAuthcode($value)
    {
        return $this->setParameter('vendorAuthcode', $value);
    }

    public function getVendorId()
    {
        return $this->getParameter('vendorId');
    }

    public function setVendorId($value)
    {
        return $this->setParameter('vendorId', $value);
    }

    public function getProductId()
    {
        return $this->getParameter('productId');
    }

    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    public function getPaymentKey()
    {
        return $this->getParameter('paymentKey');
    }

    public function setPaymentKey($value)
    {
        return $this->setParameter('paymentKey', $value);
    }

    public function getTariffId()
    {
        return $this->getParameter('tariffId');
    }

    public function setTariffId($value)
    {
        return $this->setParameter('tariffId', $value);
    }

    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }

    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }

    public function getIban()
    {
        return $this->getParameter('iban');
    }

    public function setIban($value)
    {
        return $this->setParameter('iban', $value);
    }

    public function getChosenOnly()
    {
        return $this->getParameter('chosenOnly');
    }

    public function setChosenOnly($value = true)
    {
        return $this->setParameter('chosenOnly', $value);
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
        if (self::SEPA_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest('\Omnipay\Novalnet\Message\PurchaseRequestSepa', $parameters);
        }
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
