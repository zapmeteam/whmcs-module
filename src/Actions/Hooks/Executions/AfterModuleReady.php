<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Service\Service;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class AfterModuleReady extends HookExecutionStructure
{

    public function execute(mixed $id): bool
    {
        $service      = Service::find($id);
        $this->client = $this->client($service->userid);

        $this->parse(['service' => $service]);

        $this->send();

        return true;
    }
}
