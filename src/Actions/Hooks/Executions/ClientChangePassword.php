<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class ClientChangePassword extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        $this->client = $this->client($vars['userid']);

        $this->send();

        return true;
    }
}
