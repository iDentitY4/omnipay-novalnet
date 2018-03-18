<?php

namespace Omnipay\Novalnet\Message;

class InfoportResponse extends AbstractResponse
{
    public function getSubscriptionId()
    {
        return isset($this->data->subs_id) ? $this->data->subs_id : null;
    }

    public function getSubscriptionPaidUntil()
    {
        return isset($this->data->paid_until) ? $this->data->paid_until : null;
    }
}
