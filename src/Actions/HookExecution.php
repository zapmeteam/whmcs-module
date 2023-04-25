<?php

namespace ZapMe\Whmcs\Actions;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\TemplateDto;
use ZapMe\Whmcs\Module\Template;
use ZapMe\Whmcs\Module\Configuration;

class HookExecution
{
    public function __construct(
        protected string $hook,
        protected ?int $whmcs = null,
        protected ?TemplateDto $template = null,
        protected mixed $hooks = null
    ) {
        $zapme         = (new ZapMeSdk())->toUrl(ZAPME_MODULE_API_URL);
        $configuration = (new Configuration())->dto();
        $template      = (new Template($hook))->dto()->first();
        $class         = "ZapMe\\Whmcs\\Actions\\Hooks\\" . $hook;

        $this->hooks    = new $class($hook, $zapme, $configuration, $template, $whmcs);
        $this->template = $template;
    }

    public function dispatch(mixed $vars): bool
    {
        if (!$this->template->isActive) {
            logActivity("[ZapMe][Hook: $this->hook] Envio Abortado. Template Desabilitado.");

            return false;
        }

        return $this->hooks->execute($vars);
    }
}