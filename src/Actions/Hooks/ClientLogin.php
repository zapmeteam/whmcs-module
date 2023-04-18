<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use Illuminate\Support\Collection;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class ClientLogin extends AbstractHookStructure
{
    public function execute(mixed $vars): bool
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

    private function oldest(mixed $vars): Collection
    {
        return $this->client($vars['userid']);
    }

    private function newest(mixed $vars): Collection
    {
        return $this->client($vars['user']->id);
    }
}
