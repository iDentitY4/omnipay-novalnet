<?php

namespace Omnipay\Novalnet;

use Omnipay\Common\AbstractGateway;
use Omnipay\Novalnet\Message\CompletePurchaseRequest;
use Omnipay\Novalnet\Message\PurchaseRequest;
use Omnipay\Novalnet\Message\PurchaseRequestCreditcard;
use Omnipay\Novalnet\Message\PurchaseRequestEps;
use Omnipay\Novalnet\Message\PurchaseRequestGiropay;
use Omnipay\Novalnet\Message\PurchaseRequestIdeal;
use Omnipay\Novalnet\Message\PurchaseRequestPayPal;
use Omnipay\Novalnet\Message\PurchaseRequestSepa;

/**
 * @method \Omnipay\Common\Message\ResponseInterface authorize(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface capture(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface void(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface deleteCard(array $options = array())
 * @method \Omnipay\Common\Message\ResponseInterface completePurchase(array $options = array())
 */
class Gateway extends AbstractGateway
{
    const EPS_METHOD = 50;
    const CREDITCARD_METHOD = 6;
    const GIROPAY_METHOD = 69;
    const IDEAL_METHOD = 49;
    const ONLINE_TRANSFER_METHOD = 33;
    const PAYPAL_METHOD = 34;
    const SEPA_METHOD = 0;

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
            'paymentMethod' => 0,
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

    /**
     * Start a purchase request.
     *
     * @param array $parameters An array of options
     *
     * @return \Omnipay\Novalnet\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        if (self::SEPA_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest(PurchaseRequestSepa::class, $parameters);
        }
        if (self::GIROPAY_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest(PurchaseRequestGiropay::class, $parameters);
        }
        if (self::ONLINE_TRANSFER_METHOD == $this->getPaymentMethod() || self::IDEAL_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest(PurchaseRequestIdeal::class, $parameters);
        }
        if (self::PAYPAL_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest(PurchaseRequestPayPal::class, $parameters);
        }
        if (self::EPS_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest(PurchaseRequestEps::class, $parameters);
        }
        if (self::CREDITCARD_METHOD == $this->getPaymentMethod()) {
            return $this->createRequest(PurchaseRequestCreditcard::class, $parameters);
        }

        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    function __call($name, $arguments)
    {
        // TODO: Implement @method \Omnipay\Common\Message\ResponseInterface completePurchase(array $options = array())
    }
}
