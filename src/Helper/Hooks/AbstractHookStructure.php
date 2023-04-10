<?php

namespace ZapMe\Whmcs\Helper\Hooks;

use Throwable;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Module\WhmcsClient;
use ZapMe\Whmcs\Traits\InteractWithCarbon;
use ZapMe\Whmcs\Traits\Hooks\ShareableHookConstructor;

abstract class AbstractHookStructure
{
    use InteractWithCarbon;
    use ShareableHookConstructor;

    protected bool|string|Collection|null $client = null;

    public function impersonating(): bool
    {
        return isset($_SESSION['adminid']);
    }

    protected function send(
        array $attachment = []
    ): void {
        try {
            $this->zapme
                ->withApi($this->module->api)
                ->withSecret($this->module->secret)
                ->sendMessage($this->client->get('phone'), $this->template->message, $attachment);
        } catch (Throwable $e) {
            throwlable($e);
        }
    }

    protected function client(int $id, ?string $index = null): bool|string|Collection|null
    {
        return (new WhmcsClient($id))
            ->module($this->module)
            ->get($index);
    }

    protected function log(string $message): void
    {
        $client = $this->client->get('whmcs');

        $message = str_replace(
            ['{id}', '{name}', '{hook}'],
            [$client->id, $client->fullName, $this->hook],
            $message
        );

        logActivity("[ZapMe][Hook: $this->hook] $message", $client);
    }
}
