<?php

namespace Omnipay\Novalnet\Message;

use \Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Novalnet Abstract Request
 */
abstract class AbstractRequest extends BaseAbstractRequest
{

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

    public function getLocale()
    {
        return $this->getParameter('locale');
    }

    public function setLocale($value)
    {
        return $this->setParameter('locale', $value);
    }
}
