<?php

namespace Omnipay\Novalnet\Tests\Message;

use Mockery as m;
use Omnipay\Common\CreditCard;
use Omnipay\Novalnet\Message\XmlPurchaseRequest;
use Omnipay\Novalnet\Tests\TestCase;

class XmlPurchaseRequestTest extends TestCase
{
    /**
     * @var XmlPurchaseRequest
     */
    private $request;

    protected function setUp()
    {
        $this->request = new XmlPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTestMode(true);

        $card = new CreditCard($this->getValidCard());
        $card->setEmail('test@test.de');
        $this->request->setCard($card);

        $this->request->setIban('DE24300209002411761956');

        $this->request->setVendorId(4);
        $this->request->setVendorAuthcode('JyEtHUjjbHNJwVztW6JrafIMHQvici');
        $this->request->setProductId(14);
        $this->request->setTariffId(30);

        $this->request->setAmount('5.22');
        $this->request->setTransactionId('Test23423');
        $this->request->setCurrency('eur');
        $this->request->setClientIp('127.0.0.1');
        $this->request->getCard()->setPhone('+31612345678');
        $this->request->setDescription('Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec ullamcorper nulla non metus auctor fringilla. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.');
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(522, $response->getAmount());
        $this->assertNotEquals(null, $response->getTransactionId());
    }

    public function testSendMissingDataError()
    {
        $this->setMockHttpResponse('PurchaseMissingDataError.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Missing input data ', $response->getMessage());
        $this->assertEquals(200017, $response->getCode());
    }

    public function testSendIncorrectVendorIdError()
    {
        $this->setMockHttpResponse('PurchaseIncorrectVendorIdError.txt');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('incorrect affiliate ID', $response->getMessage());
        $this->assertEquals(300041, $response->getCode());
    }
}
