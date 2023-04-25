<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class ClientChangePassword extends HookExecutionStructure
{
    public function execute(mixed $vars): bool
    {
        $this->client = $this->client($vars['userid']);

        $this->send();

        return true;
    }
}
