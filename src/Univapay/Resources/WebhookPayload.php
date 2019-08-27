<?php

namespace Univapay\Resources;

use Univapay\Enums\WebhookEvent;

class WebhookPayload
{
    public $event;
    public $data;

    public function __construct(WebhookEvent $event, $data)
    {
        $this->event = $event;
        $this->data = $data;
    }
}
