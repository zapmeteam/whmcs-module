<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\DTO\TemplateDto;
use ZapMe\Whmcs\Module\Template;

class HookExecution
{
    protected mixed $hook            = null;
    protected ?TemplateDto $template = null;

    public function __construct(string $hook, ?int $whmcs = null)
    {
        $template = $hook === 'DailyCronJob' ? null : (new Template($hook))->dto()->first();
        $class    = "ZapMe\\Whmcs\\Actions\\Hooks\\Executions\\" . $hook;

        $this->hook     = new $class($hook, $template, $whmcs);
        $this->template = $template;
    }

    public function dispatch(mixed $vars): bool
    {
        if ($this->template && !$this->template->isActive) {
            logActivity("[ZapMe][Hook: {$this->template->code}] Envio Abortado. Template Desabilitado.");

            return false;
        }

        return $this->hook->execute($vars);
    }
}
