<?php

namespace ZapMe\Whmcs\Helper\Hooks;

use Throwable;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Module\WhmcsClient;
use ZapMe\Whmcs\Actions\Log\CreateLog;
use ZapMe\Whmcs\Traits\InteractWithCarbon;
use ZapMe\Whmcs\Helper\Template\TemplateParseVariable;
use ZapMe\Whmcs\Traits\Hooks\ShareableHookConstructor;

abstract class AbstractHookStructure
{
    use InteractWithCarbon;
    use ShareableHookConstructor;

    protected bool|string|Collection|null $client = null;

    private bool $parsed = false;

    public function impersonating(): bool
    {
        return isset($_SESSION['adminid']);
    }

    protected function send(
        array $attachment = []
    ): void {
        if (!$this->client->get('new') && !$this->client->get('consent')) {
            $this->log('O cliente ({id}) {name} não deseja receber alertas via WhatsApp.');

            return;
        }

        if (!$this->parsed) {
            $this->parse();
        }

        try {
            $this->zapme
                ->withApi($this->configuration->api)
                ->withSecret($this->configuration->secret)
                ->sendMessage($this->client->get('phone'), $this->template->message, $attachment);

            if (!$this->configuration->logSystem) {
                return;
            }

            CreateLog::execute(
                $this->template->message,
                $this->template->code,
                $this->client->get('whmcs')->id
            );
        } catch (Throwable $e) {
            throwlable($e);
        }
    }

    protected function client(int $id, ?string $index = null): bool|string|Collection|null
    {
        return (new WhmcsClient($id))
            ->configuration($this->configuration)
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

    protected function parse(array $methods = []): void
    {
        $parse = new TemplateParseVariable($this->template, $this->client);

        foreach ($methods as $method => $parameters) {
            if (!method_exists($parse, $method)) {
                continue;
            }

            if (is_array($parameters)) {
                $parse->$method(...$parameters);
            } else {
                $parse->$method($parameters);
            }
        }

        $this->parsed   = true;
        $this->template = $parse->parsed();
    }
}
