<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Novalnet\XmlGateway;
use SimpleXMLElement;

class XmlPurchaseRequest extends AbstractPurchaseRequest
{
    public function getEndpoint()
    {
        return 'https://payport.novalnet.de/payport.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate(
            'vendorId',
            'vendorAuthcode',
            'productId',
            'tariffId',
            'amount',
            'currency',
            'card'
        );

        $this->validateCard(array(
            'billingFirstName',
            'billingLastName',
            'billingAddress1',
            'billingPostcode',
            'billingCity',
            'billingCountry',
            'email',
        ));

        /** @var \Omnipay\Common\CreditCard $card */
        $card = $this->getCard();

        $data = array(
            'vendor_id' => $this->getVendorId(),
            'vendor_authcode' => $this->getVendorAuthcode(),
            'product_id' => $this->getProductId(),
            'tariff_id' => $this->getTariffId(),
            'amount' => $this->getAmountInteger(),
            'payment_type' => $this->getPaymentMethod(),
            'order_no' => $this->getTransactionId(),
            'currency' => $this->getCurrency(),
            'lang' => strtoupper($this->getLocale()),
            'test_mode' => $this->getTestMode() ? 1 : 0,

            // customer details
            'customer' => array(
                'remote_ip' => $this->httpRequest->getClientIp(),
                'firstname' => $card->getBillingFirstName(),
                'lastname' => $card->getBillingLastName(),
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
                'company' => $card->getBillingCompany(),
            ),
        );

        // set description
        if ($description = $this->getDescription()) {
            $debitReason = str_split($description, 27);
            $debitReason = array_splice($debitReason, 0, 5);

            for ($i = 1; $i <= count($debitReason); $i++) {
                $data['additional_info']['debit_reason_' . $i] = $debitReason[($i - 1)];
            }
        }

        if ($this->getPaymentMethod() == XmlGateway::DIRECT_DEBIT_SEPA_METHOD) {
            $this->validate('iban');

            if ($this->getSepaDueDate()) {
                $data['sepa_due_date'] = $this->getSepaDueDate();
            }

            $data['payment_details'] = array(
                'iban' => $this->getIban(),
                'bic' => $this->getBic(),
                'bankaccount_holder' => $this->getBankaccountHolder() ?: $card->getBillingName(),
                'mandate_ref' => $this->getMandidateRef(),
            );
        }

        if ($this->getPaymentMethod() == XmlGateway::CREDITCARD_METHOD) {

            if($this->getPanHash() && $this->getUniqueId())
            {
                $this->validate('pan_hash', 'unique_id');
                $data['pan_hash'] = $this->getPanHash();
                $data['unique_id'] = $this->getUniqueId();
            }
            else
            {
                $card->validate();
                $this->validateCard(array('cvv'));

                $data['payment_details'] = array(
                    'cc_no' => $card->getNumber(),
                    'cc_exp_month' => $card->getExpiryMonth(),
                    'cc_exp_year' => $card->getExpiryYear(),
                    'cc_cvc2' => $card->getCvv(),
                    'cc_holder' => $card->getBillingName()
                );
            }
        }
        
        return $data;
    }

    public function getBic()
    {
        return $this->getParameter('bic');
    }

    public function setBic($value)
    {
        return $this->setParameter('bic', $value);
    }

    public function getBankaccountHolder()
    {
        return $this->getParameter('bankaccountHolder');
    }

    public function setBankaccountHolder($value)
    {
        return $this->setParameter('bankaccountHolder', $value);
    }

    public function getPanHash()
    {
        return $this->getParameter('pan_hash');
    }

    public function setPanHash($value)
    {
        return $this->setParameter('pan_hash', $value);
    }

    public function getUniqueId()
    {
        return $this->getParameter('unique_id');
    }

    public function setUniqueId($value)
    {
        return $this->setParameter('unique_id', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        // build xml
        $xml = new SimpleXMLElement('<nnxml></nnxml>');
        $subElement = $xml->addChild('transaction');
        $this->arrayToXml($data, $subElement);

        // send request
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $xml->asXML())->send();

        // return response
        return $this->response = new XmlPurchaseResponse($this, $httpResponse->xml()->transaction_response);
    }


    private function arrayToXml($array, &$xml_user_info)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml_user_info->addChild("$key");
                    $this->arrayToXml($value, $subnode);
                } else {
                    $subnode = $xml_user_info->addChild("item$key");
                    $this->arrayToXml($value, $subnode);
                }
            } else {
                $xml_user_info->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}
