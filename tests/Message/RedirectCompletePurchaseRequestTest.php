<?php

namespace Omnipay\Novalnet\Tests\Message;

use Mockery as m;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Novalnet\Message\CompletePurchaseRequest;
use Omnipay\Novalnet\Message\RedirectCompletePurchaseRequest;
use Omnipay\Novalnet\RedirectGateway;
use Omnipay\Tests\TestCase;

class RedirectCompletePurchaseRequestTest extends TestCase
{
    /**
     * @var \Omnipay\Novalnet\Message\RedirectCompletePurchaseRequest
     */
    protected $request;

    /**
     * Set up the CompletePurchaseRequestTest sandbox.
     */
    public function setUp()
    {
        parent::setUp();

    }

    public function testSendChosenOnlySuccess()
    {
        $request = $this->fillRequestData('ReturnChosenOnlySuccess');

        $this->request = new RedirectCompletePurchaseRequest($this->getHttpClient(), $request);
        $this->initializeRequest(RedirectGateway::IDEAL_METHOD, true);

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testSendChosenOnlyFailure()
    {
        $request = $this->fillRequestData('ReturnChosenOnlyFailed');

        $this->request = new RedirectCompletePurchaseRequest($this->getHttpClient(), $request);
        $this->initializeRequest(RedirectGateway::IDEAL_METHOD, true);

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(509006, $response->getCode());
        $this->assertEquals('Card type not accepted', $response->getMessage());
    }


    public function testSendEncodedSuccess()
    {
        $request = $this->fillRequestData('ReturnEncodedSuccess');

        $this->request = new RedirectCompletePurchaseRequest($this->getHttpClient(), $request);
        $this->initializeRequest(RedirectGateway::IDEAL_METHOD, false);

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testSendEncodedFailure()
    {
        $request = $this->fillRequestData('ReturnEncodedError');

        $this->request = new RedirectCompletePurchaseRequest($this->getHttpClient(), $request);
        $this->initializeRequest(RedirectGateway::IDEAL_METHOD, false);

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(300055, $response->getCode());
        $this->assertEquals('internal parameter transmitted incorrectly', $response->getMessage());
    }

    public function testSendEncodedSuccessInvalidHash()
    {
        $request = $this->fillRequestData('ReturnEncodedSuccess');
        $request->request->set('hash2', 'abc');

        $this->request = new RedirectCompletePurchaseRequest($this->getHttpClient(), $request);
        $this->initializeRequest(RedirectGateway::IDEAL_METHOD, false);

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(-1, $response->getCode());
        $this->assertEquals('Invalid hash', $response->getMessage());
    }

    public function testSendEncodedFailedInvalidHash()
    {
        $request = $this->fillRequestData('ReturnEncodedError');
        $request->request->set('hash2', 'abc');

        $this->request = new RedirectCompletePurchaseRequest($this->getHttpClient(), $request);
        $this->initializeRequest(RedirectGateway::IDEAL_METHOD, false);

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(300055, $response->getCode());
        $this->assertEquals('internal parameter transmitted incorrectly', $response->getMessage());
    }

    /**
     * Initialize a test request.
     *
     * @return $this
     */
    protected function initializeRequest($paymentMethod = null, $chosenOnly = false)
    {
        $options = array(
            'vendorId' => 4,
            'vendorAuthcode' => 'JyEtHUjjbHNJwVztW6JrafIMHQvici',
            'productId' => 14,
            'tariffId' => 30,
            'testMode' => 1,
            'paymentMethod' => $paymentMethod,
            'chosenOnly' => $chosenOnly,

            'amount' => 10.21,
            'currency' => 'EUR',
            'transactionId' => '12345678',

            'notifyUrl' => 'https://example.com/notify',
            'returnUrl' => 'https://example.com/success',
            'cancelUrl' => 'https://example.com/failed',
            'paymentKey' => 'a87ff679a2f3e71d9181a67b7542122c',

            // client details
            'card' => array(
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address1' => 'Streetname 1',
                'postcode' => '1234AB',
                'city' => 'Amsterdam',
                'country' => 'NL',
                'email' => 'info@example.com',
                'phone' => '+31612345678',
            ),
        );

        return $this->request->initialize($options);
    }

    protected function getRequest()
    {
        return new RedirectCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testChosenOnlySucces()
    {


    }

    protected function fillRequestData($filename)
    {
        $data = require __DIR__ . '/../Mock/' . $filename . '.php';

        $request = $this->getHttpRequest();

        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }

        return $request;
    }

    protected function getValidPostDataChosenOnly()
    {

    }

    public function getValidPostDataEncoded()
    {

    }

    public function getInvalidPostData()
    {

    }


}