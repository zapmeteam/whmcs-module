<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class TicketOpen extends AbstractHookStructure
{
    public function execute(mixed $vars): void
    {
        $ticket = Capsule::table('tbltickets')
            ->where('id', $vars['ticketid'])
            ->first();

        $this->client = $this->client($vars['userid']);

        $this->parse(['ticket' => [
            $ticket,
            $vars
        ]]);

        $this->send();
    }
}

