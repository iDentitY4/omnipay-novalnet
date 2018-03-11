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
        return 'https://payport.novalnet.de/paygate.jsp';
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
        $httpResponse = $this->httpClient->post($this->getEndpoint(), null, $data)->send();

        parse_str($httpResponse->getBody(true), $responseData);

        // return response
        return $this->response = new CapturePurchaseResponse($this, $responseData);
    }
}
