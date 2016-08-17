<?php

namespace Omnipay\Novalnet\Tests\Message;

use Mockery as m;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Novalnet\Message\CompletePurchaseResponse;
use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    /**
     * Set up the CompletePurchaseResponseTest sandbox.
     */
    public function setUp()
    {
        parent::setUp();
        $request = $this->getHttpRequest();
        $request->query->set('tid', '13380800018726060');
        $request->query->set('order_no', '12345678');

        $arguments = array($this->getHttpClient(), $request);
        $this->request = m::mock('Omnipay\Novalnet\Message\CompletePurchaseRequest[getEndpoint]', $arguments);
    }

    public function testInvalidTransactionId()
    {
        $this->request = $this->request->setTransactionId('87654321');
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseSuccess.txt');
        $arguments = array($this->request, $httpResponse->xml());
        $this->assertFalse($this->hasValidTransactionId($arguments));
    }

    public function testValidTransactionId()
    {
        $this->request = $this->request->setTransactionId('12345678');
        $httpResponse = $this->getMockHttpResponse('CompletePurchaseSuccess.txt');
        $arguments = array($this->request, $httpResponse->xml());
        $this->assertTrue($this->hasValidTransactionId($arguments));
    }

    protected function hasValidTransactionId(array $arguments)
    {
        try {
            $response = m::mock('Omnipay\Novalnet\Message\CompletePurchaseResponse[getEndpoint]', $arguments);
        } catch (InvalidResponseException $exception) {
            return false;
        }

        return true;
    }
}