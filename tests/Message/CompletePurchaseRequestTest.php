<?php

namespace Omnipay\Novalnet\Tests\Message;

use Mockery as m;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Novalnet\Message\CompletePurchaseRequest;
use Omnipay\Tests\TestCase;

class CompletePurchaseRequestTest extends TestCase
{
    /**
     * @var \Omnipay\Novalnet\Message\CompletePurchaseRequest
     */
    protected $request;

    /**
     * Set up the CompletePurchaseRequestTest sandbox.
     */
    public function setUp()
    {
        parent::setUp();
        $request = $this->getHttpRequest();
        $request->query->set('tid', '13380800018726060');
        $request->query->set('order_no', '12345678');

        $arguments = array($this->getHttpClient(), $request);
        $this->request = m::mock('Omnipay\Novalnet\Message\CompletePurchaseRequest[getEndpoint]', $arguments);
        $this->request = $this->request->setVendorId(4);
        $this->request = $this->request->setVendorAuthcode('JyEtHUjjbHNJwVztW6JrafIMHQvici');
        $this->request = $this->request->setProductId('14');
        $this->request = $this->request->setTestMode(true);
        $this->request = $this->request->setTransactionId('12345678');
    }

    /*public function testInvalidTransactionId()
    {
        var_dump($this->request->getTransactionId());
//        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');
        try {
            $response = $this->request->send();
        } catch (InvalidResponseException $exception) {
            $this->assertTrue(true);
            return;
        }

        $this->assertTrue(false);
        return;
    }*/

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CompletePurchaseSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('CompletePurchaseFailure.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(434002, $response->getCode());
    }

    /**
     * Initialize a test request.
     *
     * @return $this
     */
    protected function initializeRequest()
    {
        $options = [
            'vendorId' => 4,
            'vendorAuthcode' => 'JyEtHUjjbHNJwVztW6JrafIMHQvici',
            'productId' => 14,
            'tariffId' => 30,
            'testMode' => 1,
            'paymentMethod' => $this->paymentMethod,

            'amount' => 10.21,
            'currency' => 'EUR',
            'transactionId' => '12345678',
            'iban' => 'DE24300209002411761956',

            'notifyUrl' => 'https://example.com/notify',
            'returnUrl' => 'https://example.com/success',
            'cancelUrl' => 'https://example.com/failed',
            'paymentKey' => 'a87ff679a2f3e71d9181a67b7542122c',

            // client details
            'card' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address1' => 'Streetname 1', // note the house number in the
                'postcode' => '1234AB',
                'city' => 'Amsterdam',
                'country' => 'NL',
                'email' => 'info@example.com',
                'phone' => '+31612345678',
                'number' => '4200 0000 0000 0000',
                'expiryMonth' => date('m', strtotime('+1 month')),
                'expiryYear' => date('Y', strtotime('+1 month')),
                'cvv' => 123,
            ],
        ];

        return $this->request->initialize($options);
    }

    protected function getRequest()
    {
        return new CompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }


}