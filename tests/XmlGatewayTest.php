<?php

namespace Omnipay\Novalnet\Tests;

use Omnipay\Novalnet\AbstractGateway;
use Omnipay\Novalnet\RedirectGateway;
use Omnipay\Novalnet\XmlGateway;
use Omnipay\Tests\GatewayTestCase;

class XmlGatewayTest extends GatewayTestCase
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

        $this->gateway = new XmlGateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        $this->gateway = new XmlGateway($this->getHttpClient(), $this->getHttpRequest());

        /** @var \Omnipay\Novalnet\Message\XmlPurchaseRequest $request */
        $request = $this->gateway->purchase();

        $this->assertInstanceOf('\Omnipay\Novalnet\Message\XmlPurchaseRequest', $request);

        // default options
        $this->assertEquals(4, $request->getVendorId());
        $this->assertEquals('JyEtHUjjbHNJwVztW6JrafIMHQvici', $request->getVendorAuthcode());
        $this->assertEquals(14, $request->getProductId());
        $this->assertEquals(30, $request->getTariffId());
        $this->assertEquals(true, $request->getTestMode());
        $this->assertEquals(99, $request->getPaymentMethod());
    }
}
