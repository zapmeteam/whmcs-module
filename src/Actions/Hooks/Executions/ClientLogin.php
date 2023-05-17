<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use Illuminate\Support\Collection;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class ClientLogin extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        if ($this->impersonating()) {
            return false;
        }

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
        return $this->client($vars['user']->id);
    }
}
