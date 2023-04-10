<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class ClientLogin extends AbstractHookStructure
{
    public function execute(mixed $vars): void
    {
        logActivity('Login');
    }
}
