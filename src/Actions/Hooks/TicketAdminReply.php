<?php

namespace ZapMe\Whmcs\Actions\Hooks;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\AbstractHookStructure;

class TicketAdminReply extends AbstractHookStructure
{
    public function execute(mixed $vars): void
    {
        $ticket = Capsule::table('tbltickets')
            ->where('id', $vars['ticketid'])
            ->first();

        $this->client = $this->client($ticket->userid);

        $this->parse(['ticket' => [
            $ticket,
            $vars
        ]]);

        $this->send();
    }
}

