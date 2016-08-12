<?php

namespace Omnipay\Novalnet\Message;

class CompletePurchaseRequest extends PurchaseRequest
{
    protected $endpoint = 'https://www.Novalnet.nl/Novalnet/iDeal/RestHandler.ashx/StatusRequest';
    
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate(
            'vendorId',
            'vendorAuthcode',
            'productId',
            'tariffId'
        );

        $data = array(
            'vendor_id' => $this->getVendorId(),
            'vendor_authcode' => $this->getVendorAuthcode(),
            'product_id' => $this->getProductId(),
            'tariffId' => $this->getTariffId(),
            'tid' => $this->getTransactionReference(),
            'status_text' => $this->getStatusText(),
            'status' => $this->getStatusCode(),
        );

        return $data;
    }

    public function getTransactionReference()
    {
        return $this->httpRequest->query->get('tid');
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->endpoint, null, $data)->send();
        return $this->response = new CompletePurchaseResponse($this, $httpResponse->xml());
    }

    private function getStatusText()
    {
        return $this->httpRequest->query->get('status_text');
    }

    private function getStatusCode()
    {
        return $this->httpRequest->query->get('status');
    }
}
