<?php

namespace ZapMe\Whmcs\Actions\Hooks;

class ClientAreaPageLogin extends AbstractHookStructure
{
    public static function execute(mixed $vars): void
    {
        logActivity(var_export($vars, true));
    }
}
