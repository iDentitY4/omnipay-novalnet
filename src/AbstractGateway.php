<?php

namespace Omnipay\Novalnet;

use Omnipay\Common\AbstractGateway as CommonAbstractGateway;

abstract class AbstractGateway extends CommonAbstractGateway
{
    const CREDITCARD_METHOD = 6;
    const INVOICE_PREPAID_METHOD = 27;
    const ONLINE_TRANSFER_METHOD = 33;
    const IDEAL_METHOD = 49;
    const EPS_METHOD = 50;
    const SEPA_METHOD = 55;
    const GIROPAY_METHOD = 69;
    const PAYPAL_METHOD = 34;

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
            'paymentMethod' => null,
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
}
