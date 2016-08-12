<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Novalnet\Gateway;

/**
 * Novalnet Base Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public $endpoint;

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate(
            // general
            'vendorId',
            'vendorAuthcode',
            'productId',
            'tariffId',
            'amount',
            'currency',
            'transactionId',
            'paymentMethod',

            // customer
            'card'
        );
        $this->validateCard([
            'billingFirstName',
            'billingLastName',
            'billingAddress1',
            'billingPostcode',
            'billingCity',
            'billingCountry',
            'email',
            'phone',
        ]);

        if ($this->shouldRedirect()) {
            $this->validate('paymentKey');
        }

        // validate payment method
        if (!$this->isValidPaymentMethod($this->getPaymentMethod())) {
            throw new InvalidRequestException("The given payment method is invalid");
        }

        /** @var \Omnipay\Common\CreditCard $card */
        $card = $this->getCard();
        $data = array(
            'currency' => $this->getCurrency(),
            'order_no' => $this->getTransactionId(),
            'key' => $this->getPaymentMethod(),
            'lang' => $this->getLocale() ?: 'EN',
            'test_mode' => $this->getTestMode(),

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
        );

        $dataToEncode = [
            'auth_code' => $this->getVendorAuthcode(),
            'product' => $this->getProductId(),
            'tariff' => $this->getTariffId(),
            'amount' => $this->getAmountInteger(),
            'encoded_amount' => $this->getAmountInteger(),
            'uniqid' => $this->getTransactionId(),
            'test_mode' => $this->getTestMode(),
        ];

        if ($this->shouldRedirect() && $this->shouldEncode()) {
            $encodedData = array_map(function ($value) {
                return $this->encode($value, $this->getPaymentKey());
            }, $dataToEncode);

            $data = array_merge($data, $encodedData, [
                'vendor' => $this->getVendorId(),
//                'hash' => $this->getPaymentKey(),
            ]);
        } elseif($this->shouldRedirect() && !$this->shouldEncode()) {
            $data = array_merge($data, [
                'vendor' => $this->getVendorId(),
            ], $dataToEncode);
        } else {
            $data = array_merge($data, [
                'vendor_id' => $this->getVendorId(),
                'vendor_authcode' => $this->getVendorAuthcode(),
                'product_id' => $this->getProductId(),
                'tariff_id' => $this->getTariffId(),
                'amount' => $this->getAmountInteger(),
            ]);
        }

        // set description
        if ($description = $this->getDescription()) {
            $debitReason = str_split($description, 27);
            $debitReason = array_splice($debitReason, 0, 5);

            for ($i = 1; $i <= count($debitReason); $i++) {
                $data['additional_info']['debit_reason_' . $i] = $debitReason[($i - 1)];
            }
        }

        if ($this->shouldRedirect() && $this->getReturnUrl() && $this->getCancelUrl()) {
            $data['return_url'] = $this->getReturnUrl();
            $data['return_method'] = $this->getReturnMethod() ?: 'POST';
            $data['error_return_url'] = $this->getCancelUrl();
            $data['error_return_method'] = $this->getCancelMethod() ?: 'POST';
            $data['notify_url'] = $this->getNotifyUrl();
        } elseif ($this->shouldRedirect() && (!$this->getReturnUrl() && $this->getCancelUrl())) {
            throw new InvalidRequestException('Missing return url as parameter');
        } elseif ($this->shouldRedirect() && ($this->getReturnUrl() && !$this->getCancelUrl())) {
            throw new InvalidRequestException('Missing cancel url as parameter');
        } elseif ($this->shouldRedirect()) {
            throw new InvalidRequestException('Missing return and cancel url as parameters');
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        if ($this->shouldRedirect()) {
            return new PurchaseResponse($this, $data);
        }

        // send request
        $httpResponse = $this->httpClient->post($this->endpoint, null, $data)->send();

        // return response
        return $this->response = new PurchaseResponse($this, $httpResponse->json());
    }

    public function getDays()
    {
        return $this->getParameter('days');
    }

    public function setDays($value)
    {
        return $this->setParameter('days', $value);
    }

    public function getIncluding()
    {
        return $this->getParameter('including');
    }

    public function setIncluding($value)
    {
        return $this->setParameter('including', $value);
    }

    public function getEntranceCode()
    {
        return $this->getParameter('entranceCode') ?: $this->getTransactionId();
    }

    public function setEntranceCode($value)
    {
        return $this->setParameter('entranceCode', $value);
    }

    public function getMakeInvoice()
    {
        return $this->getParameter('makeInvoice');
    }

    public function setMakeInvoice($value)
    {
        return $this->setParameter('makeInvoice', $value);
    }

    public function getMailInvoice()
    {
        return $this->getParameter('mailInvoice');
    }

    public function setMailInvoice($value)
    {
        return $this->setParameter('mailInvoice', $value);
    }

    public function getBillingCountrycode()
    {
        return $this->getParameter('billingCountrycode');
    }

    public function setBillingCountrycode($value)
    {
        return $this->setParameter('billingCountrycode', $value);
    }

    public function getShippingCountrycode()
    {
        return $this->getParameter('shippingCountrycode');
    }

    public function setShippingCountrycode($value)
    {
        return $this->setParameter('shippingCountrycode', $value);
    }

    public function getTariffId()
    {
        return $this->getParameter('tariffId');
    }

    public function setTariffId($value)
    {
        return $this->setParameter('tariffId', $value);
    }

    public function getIban()
    {
        return $this->getParameter('iban');
    }

    public function setIban($value)
    {
        return $this->setParameter('iban', $value);
    }

    public function getSepaDueDate()
    {
        return $this->getParameter('sepaDueDate');
    }

    public function setSepaDueDate($value)
    {
        return $this->setParameter('sepaDueDate', date('Y-m-d', strtotime($value)));
    }

    public function getMandidateRef()
    {
        return $this->getParameter('mandidateRef');
    }

    public function setMandidateRef($value)
    {
        return $this->setParameter('mandidateRef', $value);
    }

    public function getPaymentKey()
    {
        return $this->getParameter('paymentKey');
    }

    public function setPaymentKey($value)
    {
        return $this->setParameter('paymentKey', $value);
    }

    public function getPaymentMethods()
    {
        return [
            Gateway::SEPA_METHOD => 'SEPA',
            Gateway::CREDITCARD_METHOD => 'Creditcard',
            Gateway::ONLINE_TRANSFER_METHOD => 'Online Transfer (Sofort)',
            Gateway::PAYPAL_METHOD => 'PayPal',
            Gateway::IDEAL_METHOD => 'iDEAL',
            Gateway::EPS_METHOD => 'eps',
            Gateway::GIROPAY_METHOD => 'giropay',
        ];
    }

    public function isValidPaymentMethod($key)
    {
        return array_key_exists($key, $this->getPaymentMethods());
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

    protected function validateCard($parameters = [])
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

    protected function encode($data, $password)
    {
        $data = trim($data);
        if ($data == '') {
            return 'Error: no data';
        }
        if (!function_exists('base64_encode') or !function_exists('pack') or !function_exists('crc32')) {
            return 'Error: func n/a';
        }
        try {
            $crc = sprintf('%u', crc32($data));# %u is a must for ccrc32 returns a signed value
            $data = $crc."|".$data;
            $data = bin2hex($data . $password);
            $data = strrev(base64_encode($data));
        } catch (Exception $e) {
            echo('Error: ' . $e);
        }

        return $data;
    }

    protected function hash1($h, $key) #$h contains encoded data
    {
        if (!$h) {
            return 'Error: no data';
        }
        if (!function_exists('md5')) {
            return 'Error: func n/a';
        }

        return md5($h['auth_code'] . $h['product_id'] . $h['tariff'] . $h['amount'] . $h['test_mode'] . $h['uniqid'] . strrev($key));
    }

    protected function encodeParams($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $password)
    {
        $auth_code = self::encode($auth_code, $password);
        $product_id = self::encode($product_id, $password);
        $tariff_id = self::encode($tariff_id, $password);
        $amount = self::encode($amount,$password);
        $test_mode = self::encode($test_mode,$password); $uniqid = self::encode($uniqid,$password);
        $hash = self::hash1(array(
            'auth_code' => $auth_code, 'product_id' => $product_id, 'tariff' => $tariff_id, 'amount' => $amount, 'test_mode' => $test_mode, 'uniqid' => $uniqid
        ), $password);
        return array($auth_code, $product_id, $tariff_id, $amount, $test_mode, $uniqid, $hash);
    }

    public function shouldEncode()
    {
        return true;
    }
}