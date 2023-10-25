<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use Illuminate\Support\Collection;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class ClientAdd extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        $this->client = $this->whmcs >= 8 ?
            $this->newest($vars) :
            $this->oldest($vars);

        $this->send();

        return true;
    }

    private function oldest($vars): Collection
    {
        return $this->client($vars['userid']);
    }

    private function newest($vars): Collection
    {
        return $this->client($vars['client_id']);
    }
}
