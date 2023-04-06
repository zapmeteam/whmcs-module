<?php

namespace ZapMe\Whmcs\Traits;

trait SendHookMessage
{
    protected function send(
        string $message,
        string $phone,
        int $client,
        array $attachment = []
    ): void {
        $result = $this->zapme
            ->withApi($this->module->api)
            ->withSecret($this->module->secret)
            ->sendMessage($phone, $message, $attachment);
    }
}
