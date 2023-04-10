<?php

namespace ZapMe\Whmcs\Actions;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\Module\Template;
use ZapMe\Whmcs\DTO\TemplateDTO;
use ZapMe\Whmcs\Module\Configuration;

class Hooks
{
    public function __construct(
        protected string $hook,
        protected int $version,
        protected ?TemplateDTO $template = null,
        protected mixed $hooks = null
    ) {
        $zapme    = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);
        $module   = (new Configuration())->fromDto();
        $template = (new Template($hook))->dto()->first();

        $class       = "ZapMe\\Whmcs\\Actions\\Hooks\\".$hook;
        $this->hooks = new $class($hook, $zapme, $module, $template, $version);

        $this->template = $template;
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
