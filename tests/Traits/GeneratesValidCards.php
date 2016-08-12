<?php namespace Omnipay\Novalnet\Tests\Traits;

trait GeneratesValidCards {
    public function getValidCard()
    {
        return [
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
        ];
    }
}