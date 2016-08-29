<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\RedirectGateway;
use Omnipay\Novalnet\XmlGateway;

/**
 * Novalnet Abstract Purchase Request
 *
 */
abstract class AbstractPurchaseRequest extends AbstractRequest
{

    public function getDays()
    {
        return $this->getParameter('days');
    }

    public function setDays($value)
    {
        return $this->setParameter('days', $value);
    }

    public function getIncluding()
    {
        return $this->getParameter('including');
    }

    public function setIncluding($value)
    {
        return $this->setParameter('including', $value);
    }

    public function getEntranceCode()
    {
        return $this->getParameter('entranceCode') ?: $this->getTransactionId();
    }

    public function setEntranceCode($value)
    {
        return $this->setParameter('entranceCode', $value);
    }

    public function getMakeInvoice()
    {
        return $this->getParameter('makeInvoice');
    }

    public function setMakeInvoice($value)
    {
        return $this->setParameter('makeInvoice', $value);
    }

    public function getMailInvoice()
    {
        return $this->getParameter('mailInvoice');
    }

    public function setMailInvoice($value)
    {
        return $this->setParameter('mailInvoice', $value);
    }

    public function getBillingCountrycode()
    {
        return $this->getParameter('billingCountrycode');
    }

    public function setBillingCountrycode($value)
    {
        return $this->setParameter('billingCountrycode', $value);
    }

    public function getShippingCountrycode()
    {
        return $this->getParameter('shippingCountrycode');
    }

    public function setShippingCountrycode($value)
    {
        return $this->setParameter('shippingCountrycode', $value);
    }

    public function getTariffId()
    {
        return $this->getParameter('tariffId');
    }

    public function setTariffId($value)
    {
        return $this->setParameter('tariffId', $value);
    }

    public function getIban()
    {
        return $this->getParameter('iban');
    }

    public function setIban($value)
    {
        return $this->setParameter('iban', $value);
    }

    public function getSepaDueDate()
    {
        return $this->getParameter('sepaDueDate');
    }

    public function setSepaDueDate($value)
    {
        return $this->setParameter('sepaDueDate', date('Y-m-d', strtotime($value)));
    }

    public function getMandidateRef()
    {
        return $this->getParameter('mandidateRef');
    }

    public function setMandidateRef($value)
    {
        return $this->setParameter('mandidateRef', $value);
    }

    public function getPaymentKey()
    {
        return $this->getParameter('paymentKey');
    }

    public function setPaymentKey($value)
    {
        return $this->setParameter('paymentKey', $value);
    }

    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }

    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }


    public function getReturnMethod()
    {
        return $this->getParameter('returnMethod');
    }

    public function setReturnMethod($value)
    {
        return $this->setParameter('returnMethod', $value);
    }

    public function getCancelMethod()
    {
        return $this->getParameter('cancelMethod');
    }

    public function setCancelMethod($value)
    {
        return $this->setParameter('cancelMethod', $value);
    }


    protected function validateCard($parameters = array())
    {
        $card = $this->getCard();
        foreach ($parameters as $parameter) {
            $value = $card->{'get' . ucfirst($parameter)}();
            if (!isset($value)) {
                throw new InvalidRequestException("The $parameter parameter is required");
            }
        }
    }

    public function getData()
    {


        return $data;
    }
}
