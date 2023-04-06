<?php

namespace ZapMe\Whmcs\Helper\Hooks;

use ZapMe\Whmcs\Traits\Hooks\ShareableHookConstructor;

abstract class AbstractHookStructure
{
    use ShareableHookConstructor;

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
