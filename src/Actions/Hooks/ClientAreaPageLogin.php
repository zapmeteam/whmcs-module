<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\Traits\SendHookMessage;
use ZapMe\Whmcs\Traits\ShareableHookConstructor;

class ClientAreaPageLogin
{
    use ShareableHookConstructor;
    use SendHookMessage;

    public static function execute(mixed $vars): void
    {
        logActivity(var_export($vars, true));
    }
}
