<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use WHMCS\User\Client;
use WHMCS\Database\Capsule;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;
use ZapMe\Whmcs\Helper\Template\TemplateParseVariable;

class ClientAreaPageLogin extends AbstractHookStructure
{

    public function execute(mixed $vars): void
    {
        if ($this->impersonating()) {
            return;
        }

        $this->client = $this->whmcs >= 8 ?
            $this->newest($vars) :
            $this->oldest();

        if (!$this->client) {
            return;
        }

        $this->template = (new TemplateParseVariable($this->template, $this->client))
            ->client()
            ->parsed();

        $this->send();
    }

    private function oldest(): bool|string|Collection|null
    {
        $log = Capsule::table('tblactivitylog')
            ->where('user', '=', 'System')
            ->latest('date')
            ->first();

        if (!$log || !Str::of($log->description)->contains('Failed Login Attempt')) {
            return null;
        }

        if (
            $this->carbon()
                ->parse($log->date)
                ->diffInMinutes($this->carbon) > 2
        ) {
            return null;
        }

        return $this->client($log->userid);
    }

    private function newest(mixed $vars): bool|string|Collection|null
    {
        if (($client = Client::where('email', '=', $vars['username'])->first()) === null) {
            return null;
        }

        return $this->client($client->id);
    }
}
