<?php

namespace ZapMe\Whmcs\Actions\Hooks\Executions;

use WHMCS\Database\Capsule;
use ZapMe\Whmcs\Helper\Hooks\HookExecutionStructure;

class TicketAdminReply extends HookExecutionStructure
{
    public function execute(mixed $vars): bool
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

        return true;
    }
}
