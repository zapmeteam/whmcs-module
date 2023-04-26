<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Service\Service;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class AfterModuleReady extends HookExecutionStructure
{
    //TODO: change parameter
    public function execute(mixed $vars): bool
    {
        $service      = Service::find($vars['service']);
        $this->client = $this->client($service->userid);

        $this->parse(['service' => $service]);

        $this->send();

        return true;
    }
}
