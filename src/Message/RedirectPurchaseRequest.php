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
            'paymentKey',
            'returnUrl',
            'cancelUrl',
            'notifyUrl'
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

        if (! $this->getUniqId()) {
            $this->setUniqId(uniqid($this->getTransactionId()));
        }

        /** @var \Omnipay\Common\CreditCard $card */
        $card = $this->getCard();
        $data = array(
            'utf8' => 1,
            'use_utf8' => 1,
            'test_mode' => $this->getTestMode() ? 1 : 0,
            'vendor' => $this->getVendorId(),
            'product' => $this->getProductId(),
            'key' => $this->getPaymentMethod(),
            'tariff' => $this->getTariffId(),
            'auth_code' => $this->getVendorAuthcode(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmountInteger(),

            // customer details
            'first_name' => $card->getBillingFirstName(),
            'last_name' => $card->getBillingLastName(),
            'email' => $card->getEmail(),
            'street' => $card->getBillingAddress1(),
            'search_in_street' => 1,
            'city' => $card->getBillingCity(),
            'zip' => $card->getBillingPostcode(),
            'country' => $card->getBillingCountry(),
            'country_code' => $card->getBillingCountry(),
            'gender' => $card->getGender() ?: 'u',
            'company' => $card->getBillingCompany(),
            'lang' => strtoupper($this->getLocale()),
            'remote_ip' => $this->httpRequest->getClientIp(),
            'tel' => $card->getBillingPhone(),
            'fax' => $card->getFax(),
            'birth_date' => $card->getBirthday(),

            'implementation' => 'PHP',
            'return_url' => $this->getReturnUrl(),
            'return_method' => $this->getReturnMethod() ?: 'POST',
            'error_return_url' => $this->getCancelUrl(),
            'error_return_method' => $this->getCancelMethod() ?: 'POST',
            'notify_url' => $this->getNotifyUrl(),

            'order_no' => $this->getTransactionId(),
            'skip_cfm' => true,
            'skip_suc' => true,
            'uniqid' => $this->getUniqId(),
            'mobile' => $card->getBillingPhone(),
            'system_name' => 'SELF_DEVELOPED',
            'system_version' => '1.0.0',
        );



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


        if (! $this->getChosenOnly() &&  $this->getPaymentMethod() == RedirectGateway::CREDITCARD_METHOD) {
            $card->validate();
            $this->validateCard(array('cvv'));

            $data['cc_no'] = $card->getNumber();
            $data['cc_exp_month'] = $card->getExpiryMonth();
            $data['cc_exp_year'] = $card->getExpiryYear();
            $data['cc_cvc2'] = $card->getCvv();
            $data['cc_holder'] = $card->getBillingName();
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


    public function getChosenOnly()
    {
        return $this->getParameter('chosenOnly');
    }

    public function setChosenOnly($value)
    {
        return $this->setParameter('chosenOnly', $value);
    }

    public function getUniqId()
    {
        return $this->getParameter('uniqId');
    }

    public function setUniqId($value)
    {
        return $this->setParameter('uniqId', $value);
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

    public function shouldEncode()
    {
        // Only direct CreditCard does not need to be encrypted
        if (!$this->getChosenOnly() && $this->getPaymentMethod() == RedirectGateway::CREDITCARD_METHOD) {
            return false;
        }

        return true;
    }
}
