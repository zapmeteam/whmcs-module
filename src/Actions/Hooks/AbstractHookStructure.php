<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\Traits\SendHookMessage;
use ZapMe\Whmcs\Traits\ShareableHookConstructor;

abstract class AbstractHookStructure
{
    use ShareableHookConstructor;
    use SendHookMessage;
}
