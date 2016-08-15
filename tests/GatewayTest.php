<?php

namespace Omnipay\Novalnet\Tests;

use Omnipay\Novalnet\Gateway;
use Omnipay\Novalnet\Message\PurchaseRequest;
use Omnipay\Novalnet\Tests\Traits\GeneratesValidCards;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    use GeneratesValidCards;

    /**
     * @var Gateway
     */
    protected $gateway;

    protected function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        /** @var \Omnipay\Novalnet\Message\PurchaseRequest $request */
        $request = $this->gateway->purchase();

        $this->assertInstanceOf(PurchaseRequest::class, $request);
    }

    public function testPurchaseCreditCard()
    {
        /** @var \Omnipay\Novalnet\Message\PurchaseRequest $request */
        $request = $this->gateway->purchase();

        $this->assertInstanceOf(PurchaseRequest::class, $request);

        // default options
        $this->assertEquals(4, $request->getVendorId());
        $this->assertEquals('JyEtHUjjbHNJwVztW6JrafIMHQvici', $request->getVendorAuthcode());
        $this->assertEquals(14, $request->getProductId());
        $this->assertEquals(30, $request->getTariffId());
        $this->assertEquals(true, $request->getTestMode());
        $this->assertEquals(99, $request->getPaymentMethod());
        $this->assertTrue($request->isValidPaymentMethod($request->getPaymentMethod()));
    }
}
