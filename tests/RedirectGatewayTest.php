<?php

namespace Omnipay\Novalnet\Tests;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\RedirectGateway;
use Omnipay\Novalnet\XmlGateway;
use Omnipay\Tests\GatewayTestCase;

class RedirectGatewayTest extends GatewayTestCase
{
    public function getValidCard()
    {
        return array(
            'firstName' => 'Max',
            'lastName' => 'Musterman',
            'number' => '4200000000000000',
            'expiryMonth' => rand(1, 12),
            'expiryYear' => gmdate('Y') + rand(1, 5),
            'cvv' => 123,
            'billingAddress1' => 'Musterstr 2',
            'billingAddress2' => 'Billsville',
            'billingCity' => 'Musterhausen',
            'billingPostcode' => '12345',
            'billingCountry' => 'DE',
            'email' => 'test@test.de',
        );
    }

    /**
     * @var AbstractGateway
     */
    protected $gateway;

    protected function setUp()
    {
        parent::setUp();

        $this->gateway = new RedirectGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        /** @var \Omnipay\Novalnet\Message\RedirectPurchaseRequest $request */
        $request = $this->gateway->purchase();

        $this->assertInstanceOf('\Omnipay\Novalnet\Message\RedirectPurchaseRequest', $request);
    }

    public function testCompletePurchase()
    {
        /** @var \Omnipay\Novalnet\Message\RedirectCompletePurchaseRequest $request */
        $request = $this->gateway->completePurchase();

        $this->assertInstanceOf('\Omnipay\Novalnet\Message\RedirectCompletePurchaseRequest', $request);
    }

    public function testPurchaseCreditCard()
    {
        /** @var \Omnipay\Novalnet\Message\RedirectPurchaseRequest $request */
        $request = $this->gateway->purchase(['paymentMethod' => RedirectGateway::CREDITCARD_METHOD]);

        $this->assertInstanceOf('\Omnipay\Novalnet\Message\RedirectPurchaseRequest', $request);

        // default options
        $this->assertEquals(4, $request->getVendorId());
        $this->assertEquals('JyEtHUjjbHNJwVztW6JrafIMHQvici', $request->getVendorAuthcode());
        $this->assertEquals(14, $request->getProductId());
        $this->assertEquals(30, $request->getTariffId());
        $this->assertEquals(false, $request->getTestMode());
        $this->assertEquals(RedirectGateway::CREDITCARD_METHOD, $request->getPaymentMethod());
    }
}
