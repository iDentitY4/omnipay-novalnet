<?php namespace Omnipay\Novalnet\Tests;

use Omnipay\Novalnet\Tests\Traits\GeneratesValidCards;
use Omnipay\Tests\TestCase as OmnipayTestCase;

class TestCase extends OmnipayTestCase
{
    use GeneratesValidCards;
}