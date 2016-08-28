<?php

namespace Omnipay\Novalnet\Tests\Message;

use Omnipay\Novalnet\Message\RedirectPurchaseRequest;
use Omnipay\Novalnet\RedirectGateway;
use Omnipay\Tests\TestCase;

class RedirectPurchaseRequestTest extends TestCase
{
    /**
     * @var \Omnipay\Novalnet\Message\RedirectPurchaseRequest
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

        $this->request = new RedirectPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    /**
     * This test verifies that a redirect URL is generated correctly.
     */
    public function testPurchaseRedirect()
    {
        $response = $this->initializeRequest()->send();

        $this->assertInstanceOf('Omnipay\Novalnet\Message\RedirectPurchaseResponse', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
    }


    /**
     * Initialize a test request.
     *
     * @return $this
     */
    protected function initializeRequest($paymentMethod = null)
    {
        $options = array(
            'vendorId' => 4,
            'vendorAuthcode' => 'JyEtHUjjbHNJwVztW6JrafIMHQvici',
            'productId' => 14,
            'tariffId' => 30,
            'testMode' => 1,
            'paymentMethod' => $paymentMethod,

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

    /**
     * @dataProvider endpointProvider
     */
    public function testEndpoints($paymentMethod, $endpointUrl)
    {
        $request = $this->initializeRequest($paymentMethod);
        $this->assertEquals($endpointUrl, $request->getEndpoint());

        $response = $request->send();
        $this->assertContains($endpointUrl, $response->getRedirectUrl());
        $this->assertTrue($response->isRedirect());
    }

    public function endpointProvider()
    {
        return array(
            array(null, 'https://payport.novalnet.de/nn/paygate.jsp'),
            array(RedirectGateway::GIROPAY_METHOD, 'https://payport.novalnet.de/giropay'),
            array(RedirectGateway::IDEAL_METHOD, 'https://payport.novalnet.de/online_transfer_payport'),
            array(RedirectGateway::ONLINE_TRANSFER_METHOD, 'https://payport.novalnet.de/online_transfer_payport'),
            array(RedirectGateway::PAYPAL_METHOD, 'https://payport.novalnet.de/paypal_payport'),
            array(RedirectGateway::EPS_METHOD, 'https://payport.novalnet.de/eps_payport'),
            array(RedirectGateway::CREDITCARD_METHOD, 'https://payport.novalnet.de/global_pci_payport'),
        );
    }
}