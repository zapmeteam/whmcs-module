<?php

namespace ZapMe\Whmcs\Traits\Hooks;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\TemplateDTO;
use ZapMe\Whmcs\DTO\ConfigurationDTO;

trait ShareableHookConstructor
{
    protected string $hook;

    protected ZapMeSdk $zapme;

    protected TemplateDTO $template;

    protected ConfigurationDTO $module;

    public function __construct(string $hook, ZapMeSdk $zapme, ConfigurationDTO $module, TemplateDTO $template)
    {
        $this->hook     = $hook;
        $this->zapme    = $zapme;
        $this->module   = $module;
        $this->template = $template;
    }
}
