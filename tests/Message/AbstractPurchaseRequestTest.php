<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Tests\TestCase;

abstract class AbstractPurchaseRequestTest extends TestCase
{
    /**
     * @var \Omnipay\Novalnet\Message\PurchaseRequest
     */
    protected $request;
    protected $paymentMethod;
    protected $redirectUrl;

    /**
     * Set up the AbstractPurchaseRequestTest sandbox.
     */
    public function setUp()
    {
        parent::setUp();

        $this->request = $this->getRequest();
        $this->paymentMethod = $this->getPaymentMethod();
        $this->redirectUrl = $this->getRedirectUrl();
    }

    /**
     * This test verifies that a redirect URL is generated correctly.
     */
    public function testPurchaseRedirect()
    {
        $response = $this->initializeRequest()->send();

        $this->assertInstanceOf('Omnipay\Novalnet\Message\PurchaseResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertContains($this->redirectUrl, $response->getRedirectUrl());
    }


    /**
     * Initialize a test request.
     *
     * @return $this
     */
    protected function initializeRequest()
    {
        $options = array(
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
            'card' => array(
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
            ),
        );

        return $this->request->initialize($options);
    }

    abstract protected function getRequest();
    abstract protected function getPaymentMethod();
    abstract protected function getRedirectUrl();
}