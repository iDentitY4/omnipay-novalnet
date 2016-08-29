<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\Helpers\RedirectEncode;
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
        $this->validate(
            'vendorId',
            'vendorAuthcode',
            'productId',
            'tariffId',
            'amount',
            'currency',
            'transactionId',
            'card',
            'paymentKey'
        );

        $this->validateCard(array(
            'billingFirstName',
            'billingLastName',
            'billingAddress1',
            'billingPostcode',
            'billingCity',
            'billingCountry',
            'email',
            'phone',
        ));

        /** @var \Omnipay\Common\CreditCard $card */
        $card = $this->getCard();
        $data = array(
            'currency' => $this->getCurrency(),
            'order_no' => $this->getTransactionId(),
            'lang' => $this->getLocale() ?: 'EN',
            'test_mode' => $this->getTestMode(),
            'skip_cfm' => true,
            'skip_suc' => true,

            // customer details
            'remote_ip' => $this->httpRequest->getClientIp(),
            'first_name' => $card->getBillingFirstName(),
            'last_name' => $card->getBillingLastName(),
            'street' => $card->getBillingAddress1(),
            'search_in_street' => 1,
            'zip' => $card->getBillingPostcode(),
            'city' => $card->getBillingCity(),
            'country' => $card->getBillingCountry(),
            'country_code' => $card->getBillingCountry(),
            'email' => $card->getEmail(),
            'mobile' => $card->getBillingPhone(),
            'tel' => $card->getBillingPhone(),
            'fax' => $card->getFax(),
            'birth_date' => $card->getBirthday(),
            'product' => $this->getProductId(),
            'tariff' => $this->getTariffId(),
            'amount' => $this->getAmountInteger(),
            'uniqid' => $this->getTransactionId(),
            'vendor' => $this->getVendorId(),
            'auth_code' => $this->getVendorAuthcode(),
        );


        if ($this->getPaymentMethod()) {
            $data['key'] = $this->getPaymentMethod();
        }

        // set description
        if ($description = $this->getDescription()) {
            $debitReason = str_split($description, 27);
            $debitReason = array_splice($debitReason, 0, 5);

            for ($i = 1; $i <= count($debitReason); $i++) {
                $data['additional_info']['debit_reason_' . $i] = $debitReason[($i - 1)];
            }
        }


        if ($this->getChosenOnly()) {
            $data['chosen_only'] = true;
        }

        if ($this->shouldEncode()) {
            $paymentKey = $this->getPaymentKey();

            $dataToEncode = array(
                'auth_code',
                'product',
                'tariff',
                'amount',
                'uniqid',
                'test_mode',
            );

            foreach ($dataToEncode as $key) {
                $data[$key] = RedirectEncode::encode($data[$key], $paymentKey);
            }

            $data['hash'] = RedirectEncode::hash1($data, $paymentKey);
        }


        if ($this->getReturnUrl() && $this->getCancelUrl()) {
            $data['return_url'] = $this->getReturnUrl();
            $data['return_method'] = $this->getReturnMethod() ?: 'POST';
            $data['error_return_url'] = $this->getCancelUrl();
            $data['error_return_method'] = $this->getCancelMethod() ?: 'POST';
            $data['notify_url'] = $this->getNotifyUrl();
        } elseif (!$this->getReturnUrl() && $this->getCancelUrl()) {
            throw new InvalidRequestException('Missing return url as parameter');
        } elseif ($this->getReturnUrl() && !$this->getCancelUrl()) {
            throw new InvalidRequestException('Missing cancel url as parameter');
        } else {
            throw new InvalidRequestException('Missing return and cancel url as parameters');
        }


        if (! $this->getChosenOnly() &&  $this->getPaymentMethod() == RedirectGateway::CREDITCARD_METHOD) {
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
        switch ($this->getPaymentMethod()) {
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
        if ($this->getChosenOnly() || !$this->getPaymentMethod() ||
            $this->getPaymentMethod() == RedirectGateway::CREDITCARD_METHOD
        ) {
            return false;
        }

        return true;
    }
}
