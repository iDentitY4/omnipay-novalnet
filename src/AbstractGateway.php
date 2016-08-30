<?php

namespace Omnipay\Novalnet;

use Omnipay\Common\AbstractGateway as CommonAbstractGateway;

abstract class AbstractGateway extends CommonAbstractGateway
{
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
            'vendorId' => null,
            'vendorAuthcode' => null,
            'productId' => null,
            'tariffId' => null,
            'testMode' => false,
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
