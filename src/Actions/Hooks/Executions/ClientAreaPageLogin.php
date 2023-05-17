<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use DateTime;
use WHMCS\User\Client;
use Illuminate\Support\Str;
use WHMCS\Database\Capsule;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class ClientAreaPageLogin extends HookExecutionStructure
{
    public function execute($vars): bool
    {
        if ($this->impersonating()) {
            return false;
        }

        $this->client = $this->whmcs >= 8 ?
            $this->newest($vars) :
            $this->oldest();

        if (!$this->client) {
            return false;
        }

        $this->send();

        return true;
    }

    private function oldest(): ?Collection
    {
        $log = Capsule::table('tblactivitylog')
            ->where('user', '=', 'System')
            ->latest('date')
            ->first();

        if (!$log || !Str::of($log->description)->contains('Failed Login Attempt')) {
            return null;
        }

        $now     = new DateTime();
        $compare = new DateTime($log->date);

        if (
            $compare->diff($now)->i > 2
        ) {
            return null;
        }

        return $this->client($log->userid);
    }

    private function newest($vars): ?Collection
    {
        if (($client = Client::where('email', '=', $vars['username'])->first()) === null) {
            return null;
        }

        return $this->client($client->id);
    }
}
