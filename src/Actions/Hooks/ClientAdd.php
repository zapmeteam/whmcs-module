<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use Illuminate\Support\Collection;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class ClientAdd extends HookExecutionStructure
{
    public function execute(mixed $vars): bool
    {
        $this->client = $this->whmcs >= 8 ?
            $this->newest($vars) :
            $this->oldest($vars);

        $this->send();

        return true;
    }

    private function oldest(mixed $vars): Collection
    {
        return $this->client($vars['userid']);
    }

    private function newest(mixed $vars): Collection
    {
        return $this->client($vars['client_id']);
    }
}
