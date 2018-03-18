<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Novalnet\InfoportGateway;
use Omnipay\Novalnet\XmlGateway;
use SimpleXMLElement;

class InfoportRequest extends AbstractRequest
{
    public function getEndpoint()
    {
        return 'https://payport.novalnet.de/nn_infoport.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate(
            'vendorId',
            'vendorAuthcode',
            'requestType'
        );

        $data = array(
            'vendor_id' => $this->getVendorId(),
            'vendor_authcode' => $this->getVendorAuthcode(),
            'request_type' => $this->getRequestType(),
            'remote_ip' => $this->httpRequest->getClientIp(),
        );

        if($this->getRequestType() === InfoportGateway::SUBSCRIPTION_STOP) {
            $this->validate('cancellationTid', 'cancellationReason');

            $data['tid'] = $this->getCancellationTid();
            $data['reason'] = $this->getCancellationReason();
        } elseif($this->getRequestType() === InfoportGateway::SUBSCRIPTION_UPDATE) {
            $this->validate('updateField', 'updateTid');

            $data['update_flag'] = $this->getUpdateField();
            $data['subs_tid'] = $this->getUpdateTid();
        } elseif($this->getRequestType() === InfoportGateway::SUBSCRIPTION_PAUSE) {
            $this->validate('pausePeriod', 'pauseTimeUnit', 'pauseTid');

            $data['pause_period'] = $this->getPausePeriod();
            $data['pause_time_unit'] = $this->getPauseTimeUnit();
            $data['tid'] = $this->getPauseTid();
        }
        
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

    public function getCancellationTid()
    {
        return $this->getParameter('cancellationTid');
    }

    public function setCancellationTid($value)
    {
        return $this->setParameter('cancellationTid', $value);
    }

    public function getCancellationReason()
    {
        return $this->getParameter('cancellationReason');
    }

    public function setCancellationReason($value)
    {
        return $this->setParameter('cancellationReason', $value);
    }

    public function getUpdateField()
    {
        return $this->getParameter('updateField');
    }

    public function setUpdateField($value)
    {
        return $this->setParameter('updateField', $value);
    }

    public function getUpdateTid()
    {
        return $this->getParameter('updateTid');
    }

    public function setUpdateTid($value)
    {
        return $this->setParameter('updateTid', $value);
    }

    public function getPausePeriod()
    {
        return $this->getParameter('pausePeriod');
    }

    public function setPausePeriod($value)
    {
        return $this->setParameter('pausePeriod', $value);
    }

    public function getPauseTimeUnit()
    {
        return $this->getParameter('pauseTimeUnit');
    }

    public function setPauseTimeUnit($value)
    {
        return $this->setParameter('pauseTimeUnit', $value);
    }

    public function getPauseTid()
    {
        return $this->getParameter('pauseTid');
    }

    public function setPauseTid($value)
    {
        return $this->setParameter('pauseTid', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        // build xml
        $xml = new SimpleXMLElement('<nnxml></nnxml>');
        $subElement = $xml->addChild('info_request');
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
