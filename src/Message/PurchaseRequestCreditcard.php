<?php

namespace Omnipay\Novalnet\Message;

class PurchaseRequestCreditcard extends PurchaseRequest
{
    public $endpoint = 'https://payport.novalnet.de/global_pci_payport';

    public function getData()
    {
        $data = parent::getData();
        $card = $this->getCard();
        $card->validate();
        $this->validateCard(['cvv']);

        $data['cc_no'] = $card->getNumber();
        $data['cc_exp_month'] = $card->getExpiryMonth();
        $data['cc_exp_year'] = $card->getExpiryYear();
        $data['cc_cvc2'] = $card->getCvv();
        $data['cc_holder'] = $card->getBillingFirstName() . ' ' . $card->getBillingLastName();

        return $data;
    }

    public function shouldEncode()
    {
        return false;
    }
}