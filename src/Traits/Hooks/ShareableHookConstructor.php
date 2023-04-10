<?php

namespace ZapMe\Whmcs\Traits\Hooks;

use ZapMeSdk\Base as ZapMeSdk;
use ZapMe\Whmcs\DTO\TemplateDTO;
use ZapMe\Whmcs\DTO\ConfigurationDTO;

trait ShareableHookConstructor
{
    public function __construct(
        protected string $hook,
        protected ZapMeSdk $zapme,
        protected ConfigurationDTO $configuration,
        protected TemplateDTO $template,
        protected ?int $whmcs = null
    ) {
    }
}
