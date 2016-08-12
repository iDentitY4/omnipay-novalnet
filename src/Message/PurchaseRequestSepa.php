<?php

namespace Omnipay\Novalnet\Message;

use SimpleXMLElement;

class PurchaseRequestSepa extends PurchaseRequest
{
    public $endpoint = 'https://payport.novalnet.de/payport.xml';

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();

        // validate custom fields
        $this->validate('iban');

        $card = $this->getCard();
        $data = $this->relocateCustomerData($data);

        $data['payment_type'] = 'DIRECT_DEBIT_SEPA';
        $data['sepa_due_date'] = $this->getSepaDueDate();

        $data['payment_details'] = [
            'iban' => $this->getIban(),
            'bankaccount_holder' => $card->getBillingFirstName() . ' ' . $card->getBillingLastName(),
            'mandate_ref' => $this->getMandidateRef(),
        ];

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
        $httpResponse = $this->httpClient->post($this->endpoint, null, $xml->asXML())->send();

        // return response
        return $this->response = new PurchaseResponseSepa($this, $httpResponse->xml()->transaction_response);
    }

    public function shouldRedirect()
    {
        return false;
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

    private function relocateCustomerData($data)
    {
        $customerDataKeys = [
            'remote_ip',
            'firstname',
            'lastname',
            'street',
            'search_in_street',
            'zip',
            'city',
            'country',
            'country_code',
            'email',
            'mobile',
            'tel',
            'fax',
            'birth_date',
        ];

        $diffKeys = [
            'first_name' => 'firstname',
            'last_name' => 'lastname',
        ];

        array_walk($data, function ($value, $key) use (&$data, $customerDataKeys, $diffKeys) {
            $oldKey = $key;
            $key = array_key_exists($key, $diffKeys) ? $diffKeys[$key] : $key;
            if (!in_array($key, $customerDataKeys)) {
                return;
            }
            $data['customer'][$key] = $value;
        });

        return $data;
    }
}
