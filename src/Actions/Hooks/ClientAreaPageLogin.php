<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;
use ZapMe\Whmcs\Helper\Hooks\HookStructureInterface;

class ClientAreaPageLogin extends AbstractHookStructure implements HookStructureInterface
{

    public function execute(mixed $vars): void
    {
        logActivity(var_export($vars, true));
        logActivity(var_export($this->template, true));
    }
}
