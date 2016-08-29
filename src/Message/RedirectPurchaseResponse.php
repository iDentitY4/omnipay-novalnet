<?php

namespace Omnipay\Novalnet\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class RedirectPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isRedirect()
    {
        return true;
    }

    /**
     * Gets the redirect target url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->request->getEndpoint();
    }

    /**
     * Get the required redirect method (either GET or POST).
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     *
     * @return array
     */
    public function getRedirectData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionReference()
    {
        return $this->request->getTransactionReference();
    }
}
