<?php

namespace ZapMe\Whmcs\Helper\Hooks;

use Throwable;
use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\TemplateDto;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Module\WhmcsClient;
use ZapMe\Whmcs\DTO\ConfigurationDto;
use ZapMe\Whmcs\Module\Configuration;
use ZapMe\Whmcs\Actions\Log\CreateModuleLog;
use ZapMe\Whmcs\Actions\Sdk\CreateSdkInstance;
use ZapMe\Whmcs\Actions\PagHiper\PagHiperBillet;
use ZapMe\Whmcs\Helper\Template\TemplateParseVariable;

class HookExecutionStructure
{
    protected ZapMeSdk $zapme;
    protected ConfigurationDto $configuration;
    protected string $hook;
    protected ?TemplateDto $template              = null;
    protected ?int $whmcs                         = null;
    private bool $parsed                          = false;
    protected bool|string|Collection|null $client = null;
    protected array $attachment                   = [];

    public function __construct(
        string $hook,
        ?TemplateDto $template = null,
        ?int $whmcs = null
    ) {
        $this->configuration = (new Configuration())->dto();
        $this->zapme         = CreateSdkInstance::execute($this->configuration);
        $this->hook          = $hook;
        $this->template      = $template;
        $this->whmcs         = $whmcs;
    }

    public function impersonating(): bool
    {
        return isset($_SESSION['adminid']);
    }

    protected function send(): void
    {
        if (!$this->client->get('new') && !$this->client->get('consent')) {
            $this->log('O cliente ({id}) {name} não deseja receber alertas via WhatsApp.');

            return;
        }

        if (!$this->parsed) {
            $this->parse();
        }

        try {
            $this->zapme->sendMessage($this->client->get('phone'), $this->template->message, $this->attachment);

            if (!$this->configuration->logSystem) {
                return;
            }

            CreateModuleLog::execute(
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
        $client = $this->client?->get('whmcs');

        $message = str_replace(
            ['{id}', '{name}', '{hook}'],
            [$client?->id, $client?->fullName, $this->hook],
            $message
        );

        if (!ZAPME_MODULE_ACTIVITY_LOG) {
            return;
        }

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

    protected function paghiper(object $invoice): void
    {
        [$message, $attachment] = PagHiperBillet::execute($this->template, $this->client->get('whmcs'), $invoice);

        $this->template->message = $message;
        $this->attachment        = $attachment;
    }
}
