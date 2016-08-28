<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\RedirectGateway;
use Omnipay\Novalnet\XmlGateway;

/**
 * Novalnet Redirect Purchase Request
 *
 * @method RedirectPurchaseResponse send()
 */
class RedirectPurchaseRequest extends AbstractPurchaseRequest
{

    public function getData()
    {
        $data = parent::getData();

        if (! $this->getChosenOnly() &&  $this->getPaymentMethod() == RedirectGateway::CREDITCARD_METHOD) {
            $card = $this->getCard();
            $card->validate();
            $this->validateCard(array('cvv'));

            $data['cc_no'] = $card->getNumber();
            $data['cc_exp_month'] = $card->getExpiryMonth();
            $data['cc_exp_year'] = $card->getExpiryYear();
            $data['cc_cvc2'] = $card->getCvv();
            $data['cc_holder'] = $card->getBillingFirstName() . ' ' . $card->getBillingLastName();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        return new RedirectPurchaseResponse($this, $data);
    }

    public function getEndpoint()
    {
        // Default Endpoint
        $endpoint = 'https://payport.novalnet.de/nn/paygate.jsp';

        //When not set, or ChosenOnly is set, use default redirect payport
        if ($this->getChosenOnly() || !$this->getPaymentMethod()) {
            return $endpoint;
        }

        // Determine endpiint based on endpoint
        switch($this->getPaymentMethod()) {
            case RedirectGateway::GIROPAY_METHOD:
                $endpoint = 'https://payport.novalnet.de/giropay';
                break;
            case RedirectGateway::IDEAL_METHOD:
            case RedirectGateway::ONLINE_TRANSFER_METHOD:
                $endpoint = 'https://payport.novalnet.de/online_transfer_payport';
                break;
            case RedirectGateway::PAYPAL_METHOD:
                $endpoint = 'https://payport.novalnet.de/paypal_payport';
                break;
            case RedirectGateway::EPS_METHOD:
                $endpoint = 'https://payport.novalnet.de/eps_payport';
                break;
            case RedirectGateway::CREDITCARD_METHOD:
                $endpoint = 'https://payport.novalnet.de/global_pci_payport';
                break;
        }

        return $endpoint;
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

    public function getChosenOnly()
    {
        return $this->getParameter('chosenOnly');
    }

    public function setChosenOnly($value)
    {
        return $this->setParameter('chosenOnly', $value);
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

    public function shouldRedirect()
    {
        return true;
    }

    public function shouldEncode()
    {
        if ( ! $this->getPaymentMethod() || RedirectGateway::CREDITCARD_METHOD == $this->getPaymentMethod()) {
            return false;
        }

        return true;
    }
}
