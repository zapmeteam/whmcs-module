<?php

namespace ZapMe\Whmcs\Actions;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\Module\Template;
use ZapMe\Whmcs\DTO\TemplateDTO;
use ZapMe\Whmcs\Module\Configuration;

class Hooks
{
    protected string $hook;

    protected TemplateDTO $template;

    protected mixed $hooks;

    public function __construct(string $hook, int $version)
    {
        $zapme    = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);
        $module   = (new Configuration())->fromDto();
        $template = (new Template($hook))->dto()->first();

        $class       = "ZapMe\\Whmcs\\Actions\\Hooks\\".$hook;
        $this->hooks = new $class($hook, $zapme, $module, $template, $version);

        $this->template = $template;
        $this->hook     = $hook;
    }

    public function dispatch(mixed $vars): void
    {
        if (!$this->template->isActive) {
            logActivity("[ZapMe][Hook: $this->hook] Envio Abortado. Template Desabilitado.");

            return;
        }

        $this->hooks->execute($vars);
    }
}
