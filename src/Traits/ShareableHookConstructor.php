<?php

namespace ZapMe\Whmcs\Traits;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\TemplateDTO;
use ZapMe\Whmcs\DTO\ConfigurationDTO;

trait ShareableHookConstructor
{
    protected ZapMeSdk $zapme;

    protected TemplateDTO $template;

    protected ConfigurationDTO $module;

    public function __construct(ZapMeSdk $zapme, ConfigurationDTO $module, TemplateDTO $template)
    {
        $this->zapme    = $zapme;
        $this->module   = $module;
        $this->template = $template;
    }
}
