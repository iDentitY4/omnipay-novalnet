<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Novalnet\XmlGateway;
use SimpleXMLElement;

class CapturePurchaseRequest extends AbstractPurchaseRequest
{
    const CAPTURE_REQUEST = 100;
    const CANCELLATION_REQUEST = 103;

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
            'transactionReference',
            'requestType'
        );

        $data = array(
            'vendor_id' => $this->getVendorId(),
            'vendor_authcode' => $this->getVendorAuthcode(),
            'product_id' => $this->getProductId(),
            'tariff_id' => $this->getTariffId(),
            'amount' => $this->getAmountInteger(),
            'key' => $this->getPaymentMethod(),
            'remote_ip' => $this->httpRequest->getClientIp(),
            'edit_status' => 1,
            'tid' => $this->getTransactionReference(),
            'status' => $this->getRequestType()
        );
        
        return $data;
    }

    public function getRequestType()
    {
        return $this->getParameter('requestType');
    }

    public function setRequestType($value)
    {
        return $this->setParameter('requestType', $value);
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
