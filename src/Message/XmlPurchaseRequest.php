<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
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
            'card',
            'iban'
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
            'vendor_id' => $this->getVendorId(),
            'vendor_authcode' => $this->getVendorAuthcode(),
            'product_id' => $this->getProductId(),
            'tariff_id' => $this->getTariffId(),
            'amount' => $this->getAmountInteger(),
            'payment_type' => 'DIRECT_DEBIT_SEPA',
            'order_no' => $this->getTransactionId(),
            'currency' => $this->getCurrency(),
            'lang' => $this->getLocale() ?: 'EN',
            'test_mode' => $this->getTestMode(),

            // customer details
            'customer' => [
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
            ],
        );

        if ($this->getSepaDueDate()) {
            $data['sepa_due_date'] = $this->getSepaDueDate();
        }

        $card = $this->getCard();
        $data['payment_details'] = array(
            'iban' => $this->getIban(),
            'bankaccount_holder' => $card->getBillingFirstName() . ' ' . $card->getBillingLastName(),
            'mandate_ref' => $this->getMandidateRef(),
        );

        return $data;
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

        if ($httpResponse->getContentType() !== 'text/xml') {
            var_dump($httpResponse->getBody(true));
            throw new InvalidResponseException('Invalid response');
        }

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
