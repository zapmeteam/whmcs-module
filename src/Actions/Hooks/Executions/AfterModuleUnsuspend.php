<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Service\Service;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class AfterModuleUnsuspend extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        $service      = Service::find($vars['params']['serviceid']);
        $this->client = $this->client($service->userid);

        $this->parse(['service' => $service]);

        $this->send();

        return true;
    }
}
