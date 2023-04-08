<?php

namespace ZapMe\Whmcs\Helper\Hooks;

use Illuminate\Support\Collection;
use ZapMe\Whmcs\Module\WhmcsClient;
use ZapMe\Whmcs\Traits\Hooks\ShareableHookConstructor;

abstract class AbstractHookStructure
{
    use ShareableHookConstructor;

    protected ?object $client = null;

    public function impersonate(): bool
    {
        return isset($_SESSION['adminid']);
    }

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

    protected function client(int $id, ?string $index = null): bool|string|Collection|null
    {
        return (new WhmcsClient($id))
            ->module($this->module)
            ->get($index);
    }

    protected function log(string $message): void
    {
        $message = str_replace(
            ['{id}', '{name}', '{hook}'],
            [$this->client->id, $this->client->fullName, $this->hook],
            $message
        );

        logActivity("[ZapMe][Hook: $this->hook] $message", $this->client);
    }
}
